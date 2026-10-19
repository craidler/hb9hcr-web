#!/usr/bin/env python3
"""
Screenshot every HTML element carrying a "data-screenshot" attribute on a fully
rendered (JavaScript) website. The attribute value is used as the filename.

    <div class="card" data-screenshot="pricing-card">...</div>   ->  pricing-card.png

Setup:
    pip install playwright
    playwright install chromium
    # On Linux servers / Docker you may also need:
    # playwright install-deps chromium

Usage:
    python element_screenshot.py https://example.com
    python element_screenshot.py https://example.com -d out --scale 2 --delay 1500
    python element_screenshot.py https://example.com --attribute data-shot
"""

import argparse
import re
import sys
from pathlib import Path

from playwright.sync_api import sync_playwright, TimeoutError as PlaywrightTimeout

IMAGE_EXTENSIONS = {".png", ".jpg", ".jpeg"}


def auto_scroll(page, step=600, pause_ms=150, max_steps=200):
    """Scroll through the page to trigger lazy-loaded content, then return to top."""
    page.evaluate(
        """async ({step, pause, maxSteps}) => {
            for (let i = 0; i < maxSteps; i++) {
                const before = window.scrollY;
                window.scrollBy(0, step);
                await new Promise(r => setTimeout(r, pause));
                if (window.scrollY === before) break;   // reached bottom
            }
            window.scrollTo(0, 0);
        }""",
        {"step": step, "pause": pause_ms, "maxSteps": max_steps},
    )


def wait_for_images_and_fonts(page, timeout_ms=15000):
    """Wait until all <img> elements are loaded and web fonts are ready."""
    page.evaluate(
        """async (timeout) => {
            const imgs = Array.from(document.images).filter(i => !i.complete);
            const imgPromises = imgs.map(i => new Promise(res => {
                i.addEventListener('load', res, {once: true});
                i.addEventListener('error', res, {once: true});
            }));
            const fonts = document.fonts ? document.fonts.ready : Promise.resolve();
            await Promise.race([
                Promise.all([...imgPromises, fonts]),
                new Promise(res => setTimeout(res, timeout)),
            ]);
        }""",
        timeout_ms,
    )


def make_filename(value, index, used):
    """Turn an attribute value into a safe, unique filename (default extension: .png)."""
    name = (value or "").strip()
    name = re.sub(r"[\\/:*?\"<>|\x00-\x1f]", "_", name)  # strip path separators & illegal chars
    name = name.lstrip(".") or f"element_{index}"

    path = Path(name)
    if path.suffix.lower() not in IMAGE_EXTENSIONS:
        stem, ext = name, ".png"
    else:
        stem, ext = path.stem, path.suffix.lower()

    candidate, n = f"{stem}{ext}", 2
    while candidate in used:  # same value used on several elements
        candidate = f"{stem}_{n}{ext}"
        n += 1
    used.add(candidate)
    return candidate


def screenshot_elements(
    url,
    attribute="data-screenshot",
    output_dir="../www/image/map",
    width=1440,
    height=900,
    scale=1.0,
    timeout=30000,
    delay=500,
    scroll=True,
    omit_background=False,
    headless=True,
    user_agent=None,
    hide=None,
):
    out_dir = Path(output_dir)
    out_dir.mkdir(parents=True, exist_ok=True)
    selector = f"[{attribute}]"
    saved, skipped = [], []

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=headless)
        context = browser.new_context(
            viewport={"width": width, "height": height},
            device_scale_factor=scale,
            user_agent=user_agent,
        )
        page = context.new_page()

        # 1) Load page and wait for network to settle (JS, XHR, etc.)
        page.goto(url, wait_until="load", timeout=timeout)
        try:
            page.wait_for_load_state("networkidle", timeout=timeout)
        except PlaywrightTimeout:
            print("Warning: network never became idle, continuing anyway.", file=sys.stderr)

        # 2) Wait until at least one tagged element exists in the DOM
        page.locator(selector).first.wait_for(state="attached", timeout=timeout)

        # 3) Trigger lazy loading, wait for images + fonts
        if scroll:
            auto_scroll(page)
        wait_for_images_and_fonts(page)

        # 4) Optionally hide annoying elements (cookie banners, chat widgets, ...)
        if hide:
            for sel in hide:
                page.add_style_tag(content=f"{sel} {{ display: none !important; }}")

        # 5) Extra settle time for animations / late JS
        if delay:
            page.wait_for_timeout(delay)

        # 6) Screenshot every tagged element
        locators = page.locator(selector)
        count = locators.count()
        print(f"Found {count} element(s) with attribute '{attribute}'.")

        used = set()
        for i in range(count):
            el = locators.nth(i)
            value = el.get_attribute(attribute)
            filename = make_filename(value, i + 1, used)
            target = out_dir / filename

            try:
                if not el.is_visible():
                    raise RuntimeError("element is not visible")
                el.scroll_into_view_if_needed()
                el.screenshot(
                    path=str(target),
                    omit_background=omit_background,
                    animations="disabled",  # freeze CSS animations for a stable image
                    timeout=timeout,
                )
                saved.append(target)
                print(f"  saved   {target}")
            except Exception as e:  # keep going if a single element fails
                skipped.append((value, str(e).splitlines()[0]))
                print(f"  skipped '{value}': {str(e).splitlines()[0]}", file=sys.stderr)

        browser.close()

    print(f"Done: {len(saved)} saved, {len(skipped)} skipped.")
    return saved, skipped


def main():
    ap = argparse.ArgumentParser(
        description="Screenshot every element with a data-screenshot attribute; the value is the filename."
    )
    ap.add_argument("url", help="Page URL")
    ap.add_argument("-d", "--output-dir", default="../www/image/map", help="Output directory")
    ap.add_argument("--attribute", default="data-screenshot", help="Attribute name to look for")
    ap.add_argument("--width", type=int, default=1440, help="Viewport width")
    ap.add_argument("--height", type=int, default=900, help="Viewport height")
    ap.add_argument("--scale", type=float, default=1.0, help="Device scale factor (2 = retina)")
    ap.add_argument("--timeout", type=int, default=30000, help="Timeout in ms")
    ap.add_argument("--delay", type=int, default=500, help="Extra wait in ms before screenshots")
    ap.add_argument("--no-scroll", action="store_true", help="Skip auto-scroll for lazy loading")
    ap.add_argument("--transparent", action="store_true", help="Transparent background (PNG)")
    ap.add_argument("--headed", action="store_true", help="Show the browser window")
    ap.add_argument("--user-agent", default=None, help="Custom User-Agent")
    ap.add_argument("--hide", action="append", help="CSS selector to hide (repeatable)")
    args = ap.parse_args()

    try:
        saved, skipped = screenshot_elements(
            url=args.url,
            attribute=args.attribute,
            output_dir=args.output_dir,
            width=args.width,
            height=args.height,
            scale=args.scale,
            timeout=args.timeout,
            delay=args.delay,
            scroll=not args.no_scroll,
            omit_background=args.transparent,
            headless=not args.headed,
            user_agent=args.user_agent,
            hide=args.hide,
        )
    except PlaywrightTimeout:
        print(f"Timeout: no element with attribute '{args.attribute}' found on {args.url}", file=sys.stderr)
        sys.exit(1)

    if not saved:
        sys.exit(1)


if __name__ == "__main__":
    main()
