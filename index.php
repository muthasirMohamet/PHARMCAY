<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>3D Anatomy Viewer</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <!-- Info Box -->
  <div id="info-box">Hover over a body part to see details.</div>

  <!-- 3D Viewer -->
  <canvas id="viewer-canvas"></canvas>

  <!-- Three.js & Viewer Script -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/0.158.0/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
  <script src="assets/viewer.js"></script>
</body>
</html>
