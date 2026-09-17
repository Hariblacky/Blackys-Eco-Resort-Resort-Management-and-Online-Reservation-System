/* ==========================================================================
   THREE.JS — Ambient firefly / dust particle field
   Lightweight (no external models) atmosphere layer for the hero section.
   ========================================================================== */
(function () {
  const canvasHost = document.querySelector('#hero-canvas');
  if (!canvasHost || !window.THREE) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(60, canvasHost.clientWidth / canvasHost.clientHeight, 0.1, 100);
  camera.position.z = 12;

  const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(canvasHost.clientWidth, canvasHost.clientHeight);
  canvasHost.appendChild(renderer.domElement);

  const PARTICLE_COUNT = window.innerWidth < 768 ? 90 : 220;
  const positions = new Float32Array(PARTICLE_COUNT * 3);
  const speeds = new Float32Array(PARTICLE_COUNT);

  for (let i = 0; i < PARTICLE_COUNT; i++) {
    positions[i * 3]     = (Math.random() - 0.5) * 26;
    positions[i * 3 + 1] = (Math.random() - 0.5) * 14;
    positions[i * 3 + 2] = (Math.random() - 0.5) * 10;
    speeds[i] = 0.15 + Math.random() * 0.35;
  }

  const geometry = new THREE.BufferGeometry();
  geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

  // Soft circular glow texture generated on a canvas (no external image needed)
  const glowCanvas = document.createElement('canvas');
  glowCanvas.width = glowCanvas.height = 64;
  const ctx = glowCanvas.getContext('2d');
  const grad = ctx.createRadialGradient(32, 32, 0, 32, 32, 32);
  grad.addColorStop(0, 'rgba(231,206,140,1)');
  grad.addColorStop(0.4, 'rgba(201,162,75,.7)');
  grad.addColorStop(1, 'rgba(201,162,75,0)');
  ctx.fillStyle = grad;
  ctx.fillRect(0, 0, 64, 64);
  const glowTexture = new THREE.CanvasTexture(glowCanvas);

  const material = new THREE.PointsMaterial({
    size: 0.22,
    map: glowTexture,
    transparent: true,
    depthWrite: false,
    blending: THREE.AdditiveBlending,
    opacity: 0.85,
  });

  const points = new THREE.Points(geometry, material);
  scene.add(points);

  const clock = new THREE.Clock();

  function animate() {
    const t = clock.getElapsedTime();
    const pos = geometry.attributes.position.array;
    for (let i = 0; i < PARTICLE_COUNT; i++) {
      pos[i * 3 + 1] += speeds[i] * 0.012;
      pos[i * 3] += Math.sin(t * 0.5 + i) * 0.003;
      if (pos[i * 3 + 1] > 7) pos[i * 3 + 1] = -7;
    }
    geometry.attributes.position.needsUpdate = true;
    points.rotation.y = Math.sin(t * 0.05) * 0.15;
    renderer.render(scene, camera);
    requestAnimationFrame(animate);
  }
  animate();

  window.addEventListener('resize', () => {
    camera.aspect = canvasHost.clientWidth / canvasHost.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(canvasHost.clientWidth, canvasHost.clientHeight);
  });
})();
