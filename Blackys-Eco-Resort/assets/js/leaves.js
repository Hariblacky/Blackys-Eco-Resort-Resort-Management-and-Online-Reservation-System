/* ==========================================================================
   FLOATING LEAVES
   Ambient drifting-leaf particles rendered on any <canvas class="leaves-canvas">.
   ========================================================================== */
(function () {
  document.querySelectorAll('.leaves-canvas').forEach((canvas) => {
    const ctx = canvas.getContext('2d');
    const host = canvas.parentElement;
    let width, height, leaves, dpr;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) return;

    const LEAF_COLORS = ['#C9A24B', '#E7CE8C', '#4FB6C0', '#1F5A40'];
    const COUNT = window.innerWidth < 768 ? 10 : 18;

    function resize() {
      dpr = Math.min(window.devicePixelRatio || 1, 2);
      width = host.clientWidth;
      height = host.clientHeight;
      canvas.width = width * dpr;
      canvas.height = height * dpr;
      canvas.style.width = width + 'px';
      canvas.style.height = height + 'px';
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    function makeLeaf() {
      return {
        x: Math.random() * width,
        y: Math.random() * -height,
        size: 8 + Math.random() * 10,
        speedY: 0.4 + Math.random() * 0.7,
        speedX: (Math.random() - 0.5) * 0.6,
        rotation: Math.random() * Math.PI * 2,
        rotSpeed: (Math.random() - 0.5) * 0.02,
        sway: Math.random() * Math.PI * 2,
        color: LEAF_COLORS[Math.floor(Math.random() * LEAF_COLORS.length)],
        opacity: 0.35 + Math.random() * 0.4,
      };
    }

    function drawLeaf(l) {
      ctx.save();
      ctx.translate(l.x, l.y);
      ctx.rotate(l.rotation);
      ctx.globalAlpha = l.opacity;
      ctx.fillStyle = l.color;
      ctx.beginPath();
      ctx.moveTo(0, -l.size);
      ctx.bezierCurveTo(l.size * 0.8, -l.size * 0.4, l.size * 0.8, l.size * 0.6, 0, l.size);
      ctx.bezierCurveTo(-l.size * 0.8, l.size * 0.6, -l.size * 0.8, -l.size * 0.4, 0, -l.size);
      ctx.fill();
      ctx.restore();
    }

    function init() {
      resize();
      leaves = Array.from({ length: COUNT }, makeLeaf);
    }
    init();
    window.addEventListener('resize', resize);

    function loop() {
      ctx.clearRect(0, 0, width, height);
      leaves.forEach(l => {
        l.sway += 0.015;
        l.y += l.speedY;
        l.x += l.speedX + Math.sin(l.sway) * 0.4;
        l.rotation += l.rotSpeed;
        if (l.y > height + 20) { l.y = -20; l.x = Math.random() * width; }
        if (l.x > width + 20) l.x = -20;
        if (l.x < -20) l.x = width + 20;
        drawLeaf(l);
      });
      requestAnimationFrame(loop);
    }
    loop();
  });
})();
