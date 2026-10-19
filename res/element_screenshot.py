#!/usr/bin/env python3
"""
Screenshot a specific HTML element of a fully rendered (JavaScript) website.

Setup:
    pip install playwright
    playwright install chromium
    # On Linux servers / Docker you may also need:
    # playwright install-deps chromium

Usage:
    python element_screenshot.py https://example.com "#main .card" -o card.png
    python element_screenshot.py https://example.com "h1" --scale 2 --delay 1500
"""

import argparse
import sys

from playwright.sync_api import sync_playwright, TimeoutError as PlaywrightTimeout


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


def screenshot_element(
    url,
    selector,
    output="element.png",
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

        # 2) Wait until the target element exists and is visible
        locator = page.locator(selector).first
        locator.wait_for(state="visible", timeout=timeout)

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

        # 6) Screenshot only that element (Playwright scrolls it into view)
        locator.scroll_into_view_if_needed()
        locator.screenshot(
            path=output,
            omit_background=omit_background,
            animations="disabled",  # freeze CSS animations for a stable image
        )

        browser.close()
    print(f"Saved: {output}")


def main():
    ap = argparse.ArgumentParser(description="Screenshot a specific element of a rendered website.")
    ap.add_argument("url", help="Page URL")
    ap.add_argument("selector", help="CSS selector (or Playwright selector, e.g. 'text=Hello')")
    ap.add_argument("-o", "--output", default="element.png", help="Output file (png/jpeg)")
    ap.add_argument("--width", type=int, default=1440, help="Viewport width")
    ap.add_argument("--height", type=int, default=900, help="Viewport height")
    ap.add_argument("--scale", type=float, default=1.0, help="Device scale factor (2 = retina)")
    ap.add_argument("--timeout", type=int, default=30000, help="Timeout in ms")
    ap.add_argument("--delay", type=int, default=500, help="Extra wait in ms before screenshot")
    ap.add_argument("--no-scroll", action="store_true", help="Skip auto-scroll for lazy loading")
    ap.add_argument("--transparent", action="store_true", help="Transparent background (PNG)")
    ap.add_argument("--headed", action="store_true", help="Show the browser window")
    ap.add_argument("--user-agent", default=None, help="Custom User-Agent")
    ap.add_argument("--hide", action="append", help="CSS selector to hide (repeatable)")
    args = ap.parse_args()

    try:
        screenshot_element(
            url=args.url,
            selector=args.selector,
            output=args.output,
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
        print(f"Timeout: element '{args.selector}' not found/visible on {args.url}", file=sys.stderr)
        sys.exit(1)


if __name__ == "__main__":
    main()
