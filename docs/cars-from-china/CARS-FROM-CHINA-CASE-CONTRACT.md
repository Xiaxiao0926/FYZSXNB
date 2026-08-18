# Cars from China — Case Contract (evidence-first rules)

> Applies to every article that will be filed under `fyz_vehicle` /
> `fyz_research_type`, especially `owner-cases`, `common-problems` and
> `case-study`. These are **binding rules**, not guidelines.

## 1. Core principle

**No claim without a source chain.** Every factual statement about a specific
vehicle, its faults, parts compatibility, versions or owner experience must
trace back to at least one named, linkable source. If a claim cannot be
sourced, it is not written.

## 2. What is a valid source (hierarchy)

1. **Primary (best):** official documents — owner's manuals, homologation
   documents, service bulletins (TSB), official configurator / parts catalogs,
   manufacturer press releases.
2. **Strong secondary:** Chinese owner reports on named forums/communities
   (e.g. Dongchedi, Autohome) with a link and date; Chinese used-car listings
   (named marketplace) as market evidence, never as a "fact".
3. **Corroborating:** cross-market comparison data (CN vs export/other-market
   specs), Russian-side sources when they add independent confirmation.
4. **Not acceptable:** anonymous hearsay, "people say", AI-generated example
   cases, invented owner interviews, screenshots that cannot be verified.

## 3. Case rules (owner-cases, case-study)

- Every case must be **real**: tied to a named source (platform + link +
  approximate date) or to our own verified correspondence (then labeled as
  such).
- Cases must be attributed with a visible citation link `[N]` per article,
  resolved to the article's `REF_URLS` (GitHub-hosted source list), matching
  the established cluster publishing pattern.
- **Never fabricate** counts ("3 of 5 owners…"), percentages, or sample sizes.
- A single case may be reported only as a single case, not generalized.
- RU-side reporting of Chinese sources must state the source market
  explicitly (e.g. "по отзывам на Autohome, март 2025").

## 4. Common problems rules (common-problems)

- A "common problem" may only be listed when there is **published evidence**
  (TSB, service documentation, or multiple independent owner reports, each
  cited).
- Each listed problem needs: what it is, which versions/engines/transmissions
  it affects (only when documented), typical symptoms, and the citation(s).
- If evidence is thin, write it as "reported cases" with the actual number of
  sources, not as a generalized fault.
- **Prohibited:** copying problem lists from other sites without verification;
  converting marketing claims into fault claims; writing problems "because
  every car has them".

## 5. Parts & compatibility rules (parts-compatibility)

- Part numbers must come from an official catalog or the manufacturer, with a
  link; never guessed.
- Compatibility statements must be scoped (which market version, which VIN
  range if documented, which engine code).
- "Interchangeable with X" requires a documented source; otherwise say
  "не подтверждено" / "not confirmed".

## 6. Market-version rules (market-version)

- CN vs export comparisons must use named specs per market, each cited.
- Label clearly which market a spec belongs to; do not blend markets.
- If a difference is unverified, state that it is unverified.

## 7. Page-level integrity

- A model page is **not published to index** until it has at least the launch
  gate content (see Content Contract §5).
- Empty sections are suppressed by the renderer — never "fill" them with
  placeholders or generic text in production.
- Every article gets at least one `fyz_research_type` term and exactly the
  vehicle terms it actually concerns.

## 8. Review gate

Before any case/common-problem/market-version article ships:

- [ ] every factual sentence has a citation `[N]`
- [ ] `REF_URLS` list contains all N links (working, named)
- [ ] no invented numbers, counts or cases
- [ ] RU articles carry category 54 (Russian Library); EN never does
- [ ] single `<h1>`; no duplicate H1s
