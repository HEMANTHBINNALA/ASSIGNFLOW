let mediaRecorder;
let audioChunks = [];
let videoChunks = [];

// Audio Recording
const startAudioBtn = document.getElementById('start-audio');
const stopAudioBtn = document.getElementById('stop-audio');
const audioPlayback = document.getElementById('audio-playback');
const audioInput = document.getElementById('audio-blob');

if (startAudioBtn) {
    startAudioBtn.addEventListener('click', async () => {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = event => {
            audioChunks.push(event.data);
        };

        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const audioUrl = URL.createObjectURL(audioBlob);
            audioPlayback.src = audioUrl;

            // Create a file object to upload
            const file = new File([audioBlob], "recording.webm", { type: "audio/webm" });

            // We need to use DataTransfer to set file input
            const container = new DataTransfer();
            container.items.add(file);
            document.getElementById('audio-file-input').files = container.files;

            audioChunks = [];
        };

        mediaRecorder.start();
        startAudioBtn.disabled = true;
        stopAudioBtn.disabled = false;
        startAudioBtn.classList.add('recording');
    });
}

if (stopAudioBtn) {
    stopAudioBtn.addEventListener('click', () => {
        mediaRecorder.stop();
        startAudioBtn.disabled = false;
        stopAudioBtn.disabled = true;
        startAudioBtn.classList.remove('recording');
    });
}

// Video Recording
const startVideoBtn = document.getElementById('start-video');
const stopVideoBtn = document.getElementById('stop-video');
const videoPlayback = document.getElementById('video-playback');
const videoPreview = document.getElementById('video-preview');

if (startVideoBtn) {
    startVideoBtn.addEventListener('click', async () => {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        videoPreview.srcObject = stream;
        videoPreview.play();
        videoPreview.style.display = 'block';
        videoPlayback.style.display = 'none';

        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = event => {
            videoChunks.push(event.data);
        };

        mediaRecorder.onstop = () => {
            const videoBlob = new Blob(videoChunks, { type: 'video/webm' });
            const videoUrl = URL.createObjectURL(videoBlob);
            videoPlayback.src = videoUrl;
            videoPlayback.style.display = 'block';
            videoPreview.style.display = 'none';

            // Create a file object to upload
            const file = new File([videoBlob], "video_recording.webm", { type: "video/webm" });

            // We need to use DataTransfer to set file input
            const container = new DataTransfer();
            container.items.add(file);
            document.getElementById('video-file-input').files = container.files;

            videoChunks = [];
            stream.getTracks().forEach(track => track.stop()); // Stop camera
        };

        mediaRecorder.start();
        startVideoBtn.disabled = true;
        stopVideoBtn.disabled = false;
        startVideoBtn.classList.add('recording');
    });
}

if (stopVideoBtn) {
    stopVideoBtn.addEventListener('click', () => {
        mediaRecorder.stop();
        startVideoBtn.disabled = false;
        stopVideoBtn.disabled = true;
        startVideoBtn.classList.remove('recording');
    });
}
