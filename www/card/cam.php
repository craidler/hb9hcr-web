<div class="card-header">
    <h2 class="card-title">Cam</h2>
</div>
<div class="card-body">
    <div class="row">
        <div class="col-6">
            <figure class="polaroid">
                <picture>
                    <video id="cam-video" style="width:100%; min-height: 233px" autoplay playsinline></video>
                </picture>
                <figcaption>
                    webcam
                </figcaption>
            </figure>
            <div class="px-3 mt-5">
                <button class="btn btn-secondary w-100" id="cam-start">start camera</button>
            </div>
        </div>
        <div class="col-6">
            <figure class="polaroid">
                <picture>
                    <canvas id="cam-canvas" style="width:100%; min-height: 233px"></canvas>
                </picture>
                <figcaption>
                    captured
                </figcaption>
            </figure>
            <div class="px-3 mt-5">
                <button class="btn btn-secondary w-100" id="cam-take">take snapshot</button>
            </div>
        </div>
    </div>
</div>
<script>
    const cam = {
        interval: null,
        canvas: document.getElementById('cam-canvas'),
        start: document.getElementById('cam-start'),
        video: document.getElementById('cam-video'),
        take: document.getElementById('cam-take'),
    };

    function takeSnapshot() {
        // Only take a picture if the video stream has metadata/dimensions ready
        if (cam.video.videoWidth > 0 && cam.video.videoHeight > 0) {
            let d = new Date(), ctx = cam.canvas.getContext('2d');
            cam.canvas.width = cam.video.videoWidth;
            cam.canvas.height = cam.video.videoHeight;
            ctx.drawImage(cam.video, 0, 0, cam.canvas.width, cam.canvas.height);
            ctx.font = '18px monospace';
            ctx.shadowColor = 'rgba(0, 0, 0, .5)'; // Shadow color with transparency
            ctx.shadowBlur = 0;                     // Blur radius in pixels
            ctx.shadowOffsetX = 1;                  // Horizontal distance in pixels
            ctx.shadowOffsetY = 1;                  // Vertical distance in pixels
            ctx.textAlign = 'center';
            ctx.fillStyle = '#ffffffff';
            ctx.fillText(d.toISOString(), cam.canvas.width / 2, cam.canvas.height - 18);

            cam.canvas.toBlob((blob) => {
                if (!blob) {
                    console.error("Failed to generate image blob.");
                    return;
                }
                
                const formData = new FormData();
                formData.append('webcam_snapshot', blob, 'snapshot.jpg');                
                fetch('/cam/upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log("server response:", data);
                })
                .catch(error => {
                    console.error("upload failed:", error);
                });
                
            }, 'image/jpeg', 1); 
        }
    }

    cam.start.addEventListener('click', async () => {
        if (cam.interval) {
            clearInterval(cam.interval);
            cam.interval = null;
            cam.start.innerHTML = 'start camera';
            cam.take.disabled = true;
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            cam.video.srcObject = stream;
            cam.take.disabled = false;
            cam.interval = setInterval(takeSnapshot, 15000);
            cam.video.addEventListener('loadedmetadata', () => {
                setTimeout(takeSnapshot, 500);
            }, { once: true });

            cam.start.innerHTML = 'stop camera';
        } catch (err) {
            console.error("error accessing webcam: ", err);
            alert("could not access webcam. make sure permissions are granted.");
        }
    });

    cam.take.addEventListener('click', takeSnapshot);
</script>