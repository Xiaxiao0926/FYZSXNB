(function () {
  'use strict';

  var content = document.querySelector('.single-post .nv-content-wrap');
  var header = document.querySelector('.single-post .entry-header');

  if (!content || !header) {
    return;
  }

  var headings = Array.prototype.slice.call(content.querySelectorAll('h2, h3'))
    .filter(function (heading) {
      return heading.textContent.trim().length > 0;
    });

  if (headings.length < 2 || document.querySelector('.fyz-article-toc')) {
    return;
  }

  var usedIds = {};
  var list = document.createElement('ul');

  headings.forEach(function (heading, index) {
    var base = heading.id || heading.textContent
      .toLowerCase()
      .replace(/[^a-z0-9\u0400-\u04ff]+/g, '-')
      .replace(/^-|-$/g, '') || 'section';
    var id = base;

    if (usedIds[id]) {
      usedIds[id] += 1;
      id = base + '-' + usedIds[base];
    } else {
      usedIds[id] = 1;
    }

    if (!heading.id) {
      heading.id = 'fyz-' + id + '-' + index;
    }

    var item = document.createElement('li');
    var link = document.createElement('a');
    link.href = '#' + heading.id;
    link.textContent = heading.textContent.trim();
    item.className = heading.tagName.toLowerCase() === 'h3' ? 'fyz-toc-level-3' : 'fyz-toc-level-2';
    item.appendChild(link);
    list.appendChild(item);
  });

  var aside = document.createElement('aside');
  aside.className = 'fyz-article-toc';
  aside.setAttribute('aria-label', 'Article contents');

  var details = document.createElement('details');
  details.open = true;

  var summary = document.createElement('summary');
  summary.textContent = 'In this report';
  details.appendChild(summary);
  details.appendChild(list);
  aside.appendChild(details);
  header.insertAdjacentElement('afterend', aside);
}());
