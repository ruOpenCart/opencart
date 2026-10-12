(function () {
  if (window.OcnSlider) {
    return;
  }

  function boot() {
    document.querySelectorAll('[data-ocn-slider]').forEach(mount);
  }

  function mount(root) {
    if (root.getAttribute('data-ocn-ready') === '1') {
      return;
    }

    root.setAttribute('data-ocn-ready', '1');

    var viewport = root.querySelector('[data-ocn-viewport]');
    var track = root.querySelector('[data-ocn-track]');
    var slides = Array.prototype.slice.call(root.querySelectorAll('[data-ocn-slide]'));
    var prev = root.querySelector('[data-ocn-prev]');
    var next = root.querySelector('[data-ocn-next]');
    var dots = Array.prototype.slice.call(root.querySelectorAll('[data-ocn-dot]'));
    var count = slides.length;

    if (!viewport || !track || count === 0) {
      return;
    }

    var index = 0;
    var timer = 0;
    var hover = false;
    var dragging = false;
    var pointerId = null;
    var startX = 0;
    var startY = 0;
    var deltaX = 0;
    var axis = '';
    var suppressClick = false;
    var effect = root.getAttribute('data-effect') === 'fade' ? 'fade' : 'slide';
    var autoplay = root.getAttribute('data-autoplay') === '1' && count > 1;
    var pause = root.getAttribute('data-pause') !== '0';
    var interval = parseInt(root.getAttribute('data-interval') || '5000', 10);
    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!interval || interval < 1000) {
      interval = 5000;
    }

    function paint(animate) {
      if (effect === 'slide') {
        track.style.transition = animate === false ? 'none' : '';
        track.style.transform = 'translate3d(' + (-index * 100) + '%,0,0)';
      }

      slides.forEach(function (slide, slideIndex) {
        var active = slideIndex === index;

        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        slide.querySelectorAll('a').forEach(function (link) {
          if (active) {
            link.removeAttribute('tabindex');
          } else {
            link.setAttribute('tabindex', '-1');
          }
        });
      });

      dots.forEach(function (dot, dotIndex) {
        var active = dotIndex === index;

        dot.classList.toggle('is-active', active);

        if (active) {
          dot.setAttribute('aria-current', 'true');
        } else {
          dot.removeAttribute('aria-current');
        }
      });
    }

    function stop() {
      if (timer) {
        window.clearInterval(timer);
        timer = 0;
      }
    }

    function start() {
      stop();

      if (!autoplay || reduced || hover || dragging || document.hidden) {
        return;
      }

      timer = window.setInterval(function () {
        go(index + 1, false);
      }, interval);
    }

    function go(nextIndex, fromUser) {
      if (count < 2) {
        return;
      }

      index = ((nextIndex % count) + count) % count;
      paint(true);

      if (fromUser) {
        start();
      }
    }

    if (prev) {
      prev.addEventListener('click', function () {
        go(index - 1, true);
      });
    }

    if (next) {
      next.addEventListener('click', function () {
        go(index + 1, true);
      });
    }

    dots.forEach(function (dot, dotIndex) {
      dot.addEventListener('click', function () {
        go(dotIndex, true);
      });
    });

    if (pause) {
      root.addEventListener('pointerenter', function (event) {
        if (event.pointerType !== 'mouse') {
          return;
        }

        hover = true;
        stop();
      });

      root.addEventListener('pointerleave', function (event) {
        if (event.pointerType !== 'mouse') {
          return;
        }

        hover = false;
        start();
      });
    }

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        stop();
      } else {
        start();
      }
    });

    viewport.addEventListener('pointerdown', function (event) {
      if (count < 2) {
        return;
      }

      if (event.pointerType === 'mouse' && event.button !== 0) {
        return;
      }

      dragging = true;
      pointerId = event.pointerId;
      startX = event.clientX;
      startY = event.clientY;
      deltaX = 0;
      axis = '';
      suppressClick = false;
      stop();

      if (viewport.setPointerCapture) {
        try {
          viewport.setPointerCapture(event.pointerId);
        } catch (ignore) {}
      }
    });

    viewport.addEventListener('pointermove', function (event) {
      if (!dragging || event.pointerId !== pointerId) {
        return;
      }

      var dx = event.clientX - startX;
      var dy = event.clientY - startY;

      if (!axis) {
        if (Math.abs(dx) < 6 && Math.abs(dy) < 6) {
          return;
        }

        axis = Math.abs(dx) > Math.abs(dy) ? 'x' : 'y';
      }

      if (axis !== 'x') {
        return;
      }

      deltaX = dx;

      if (effect === 'slide') {
        track.style.transition = 'none';
        track.style.transform = 'translate3d(calc(' + (-index * 100) + '% + ' + dx + 'px),0,0)';
      }
    });

    function endDrag(event) {
      if (!dragging || (event && event.pointerId !== pointerId)) {
        return;
      }

      dragging = false;

      var width = viewport.clientWidth || 1;
      var passed = axis === 'x' && Math.abs(deltaX) > Math.max(48, width * 0.15);

      if (passed) {
        suppressClick = true;
        go(index + (deltaX < 0 ? 1 : -1), true);
      } else {
        paint(true);
        start();
      }

      axis = '';
      deltaX = 0;
    }

    viewport.addEventListener('pointerup', endDrag);
    viewport.addEventListener('pointercancel', endDrag);

    viewport.addEventListener('click', function (event) {
      if (!suppressClick) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      suppressClick = false;
    }, true);

    paint(false);

    if (effect === 'slide') {
      window.requestAnimationFrame(function () {
        track.style.transition = '';
      });
    }

    start();
  }

  window.OcnSlider = { boot: boot };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
