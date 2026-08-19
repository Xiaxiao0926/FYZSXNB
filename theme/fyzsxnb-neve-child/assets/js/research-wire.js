(function () {
  'use strict';

  if (window.__fyzTocLoaded) { return; }
  window.__fyzTocLoaded = true;

  /**
   * FYZSXNB Article TOC (UI V2 0.3.4).
   * Vanilla JS, deferred, feature-scoped: only runs when a single-post
   * content area exists. The TOC is progressive enhancement — with JS off
   * the article body stays fully readable and no empty sidebar appears.
   * Bilingual labels come from the body class set by functions.php
   * (fyz-lang-ru / fyz-lang-en).
   */

  var content = document.querySelector('.single-post .nv-content-wrap');
  var header = document.querySelector('.single-post .entry-header');

  if (!content || !header) {
    return;
  }

  var isRu = document.body.classList.contains('fyz-lang-ru');
  var LABELS = isRu
    ? { summary: 'Содержание', aria: 'На этой странице', back: 'Наверх' }
    : { summary: 'Contents', aria: 'On this page', back: 'Back to top' };

  var headings = Array.prototype.slice.call(content.querySelectorAll('h2, h3'))
    .filter(function (heading) {
      return heading.textContent.trim().length > 0;
    });

  // H4 and below never enter the TOC; a TOC needs at least two entries.
  if (headings.length < 2 || document.querySelector('.fyz-article-toc')) {
    return;
  }

  // Collect every id already present on the page to keep anchors unique.
  var taken = {};
  Array.prototype.slice.call(document.querySelectorAll('[id]')).forEach(function (el) {
    taken[el.id] = true;
  });

  var usedBase = {};
  var list = document.createElement('ul');

  headings.forEach(function (heading, index) {
    var text = heading.textContent.trim();
    var base = heading.id || text
      .toLowerCase()
      .replace(/[^a-z0-9\u0400-\u04ff]+/g, '-')
      .replace(/^-|-$/g, '') || 'section';
    var id = base;

    if (taken[id] || usedBase[base]) {
      var n = (usedBase[base] || 1) + 1;
      usedBase[base] = n;
      id = base + '-' + n;
      while (taken[id]) {
        n += 1;
        usedBase[base] = n;
        id = base + '-' + n;
      }
    } else {
      usedBase[base] = 1;
    }

    heading.id = 'fyz-' + id + '-' + index;
    taken[heading.id] = true;

    var item = document.createElement('li');
    var link = document.createElement('a');
    link.href = '#' + heading.id;
    link.textContent = text;
    item.className = heading.tagName.toLowerCase() === 'h3' ? 'fyz-toc-level-3' : 'fyz-toc-level-2';
    item.appendChild(link);
    list.appendChild(item);
  });

  var aside = document.createElement('aside');
  aside.className = 'fyz-article-toc';
  aside.setAttribute('aria-label', LABELS.aria);

  var details = document.createElement('details');
  // Desktop: open by default; below 960px it becomes a collapsible block.
  details.open = window.innerWidth >= 960;

  var summary = document.createElement('summary');
  summary.textContent = LABELS.summary;
  details.appendChild(summary);
  details.appendChild(list);

  var back = document.createElement('a');
  back.className = 'fyz-toc-back';
  back.href = '#fyz-article-top';
  back.textContent = LABELS.back;
  details.appendChild(back);

  aside.appendChild(details);

  if (!header.id) {
    header.id = 'fyz-article-top';
  }

  header.insertAdjacentElement('afterend', aside);

  // Signal the CSS layout (TOC column + body column) — only when a TOC exists,
  // so JS-off pages keep the plain single-column reading layout.
  var wrap = document.querySelector('.nv-single-post-wrap.col') || content.parentElement;
  if (wrap) {
    wrap.classList.add('fyz-has-toc');
  }
}());