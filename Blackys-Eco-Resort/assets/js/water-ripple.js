/* ==========================================================================
   WATER RIPPLE EFFECT
   Lightweight canvas2D ripple rings triggered on move/click/touch, layered
   over any element with #water-ripple-canvas + .ripple-section wrapper.
   ========================================================================== */
(function () {
  const canvas = document.querySelector('#water-ripple-canvas');
  if (!canvas) return;
  const section = canvas.closest('.ripple-section');
  const ctx = canvas.getContext('2d');
  let ripples = [];
  let width, height, dpr;

  function resize() {
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    width = section.clientWidth;
    height = section.clientHeight;
    canvas.width = width * dpr;
    canvas.height = height * dpr;
    canvas.style.width = width + 'px';
    canvas.style.height = height + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
  }
  resize();
  window.addEventListener('resize', resize);

  function addRipple(x, y) {
    ripples.push({ x, y, radius: 0, alpha: 0.55, maxRadius: 90 + Math.random() * 60 });
    if (ripples.length > 40) ripples.shift();
  }

  let lastMove = 0;
  canvas.addEventListener('pointermove', (e) => {
    const now = Date.now();
    if (now - lastMove < 90) return; // throttle
    lastMove = now;
    const rect = canvas.getBoundingClientRect();
    addRipple(e.clientX - rect.left, e.clientY - rect.top);
  });
  canvas.addEventListener('pointerdown', (e) => {
    const rect = canvas.getBoundingClientRect();
    for (let i = 0; i < 3; i++) {
      setTimeout(() => addRipple(e.clientX - rect.left, e.clientY - rect.top), i * 90);
    }
  });

  function drawRipple(r) {
    ctx.beginPath();
    ctx.strokeStyle = `rgba(232, 244, 246, ${r.alpha})`;
    ctx.lineWidth = 2;
    ctx.arc(r.x, r.y, r.radius, 0, Math.PI * 2);
    ctx.stroke();

    ctx.beginPath();
    ctx.strokeStyle = `rgba(79, 182, 192, ${r.alpha * 0.7})`;
    ctx.lineWidth = 1;
    ctx.arc(r.x, r.y, r.radius * 0.7, 0, Math.PI * 2);
    ctx.stroke();
  }

  function loop() {
    ctx.clearRect(0, 0, width, height);
    ripples.forEach(r => {
      r.radius += 1.6;
      r.alpha *= 0.965;
      if (r.alpha > 0.01) drawRipple(r);
    });
    ripples = ripples.filter(r => r.alpha > 0.01 && r.radius < r.maxRadius);
    requestAnimationFrame(loop);
  }
  loop();

  // gentle ambient ripple every couple seconds so the section feels alive
  setInterval(() => {
    if (document.hidden) return;
    addRipple(Math.random() * width, Math.random() * height);
  }, 2200);
})();
