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

## 9. Case grading (binding thresholds, set by the editorial lead 2026-08-18)

No single forum post may ever be called a "common problem" directly. Internal
grading before anything may be written:

- **CASE** — one real owner case with a traceable source. May enter the
  research library, may be reported as an individual case, but **cannot** be
  called a common problem.
- **REPEATED ISSUE** — several **independent** real cases, and the
  model/year/drivetrain of the cases line up with each other. Only then does
  it enter the candidate-issue list.
- **PATTERN** — repeated issues + technical explanation, repair outcome,
  part revision, or official documentation supporting it. Only a PATTERN may
  be written as `Common Problems / Что ломается`.

**Hearsay rule:** a topic being widely repeated across forums is NOT multiple
cases. Rumors retold by many users count as one chain of transmission, not as
case count. Only independently documented owner experiences count.

**Search categories ≠ presumed faults:** investigation directions (e.g.
engine, DSG/transmission, AWD, cooling, emissions/GPF, suspension/steering,
electronics, infotainment, ADAS/radar/camera, AC, body/interior, parts
revisions, China-version features, high-mileage, hard-to-identify parts) are
**search taxonomy only**. A direction may conclude with zero findings; the
conclusion "no repeated pattern — keep as individual cases" is a valid outcome
and must be recorded as such.

## 10. Research pack record format (agreed matrixes)

Case Matrix (one row per deduplicated case, at research time):

| Case ID | 年款/Year | 动力/Drivetrain | 里程/Mileage | 症状/Symptom | 最终诊断/Final diagnosis | 维修/Repair | 零件/Part (OE) | 来源/Source | 独立案例/Independent | 俄罗斯出现/RU occurrence |

Issue Matrix (one row per candidate issue):

| Issue | 独立案例数/ind. cases | 年款范围/year range | 是否确认原因/Cause confirmed | 有无维修结果/Repair result | 有无零件号/Part no. | RU 案例 | 是否值得写/Worth writing (YES/MAYBE/NO) |

Decision rule: articles are decided **only after** both matrixes exist for a
vehicle. Nothing is written from raw post collections alone.

## 11. Vehicle roadmap + market anchors (2026-08-18)

- First-round order: **Volkswagen Tayron → Toyota Corolla → Audi Q3** (covers
  German JV SUV / Japanese JV sedan / premium CN-built model).
- Tayron-first rationale (user-provided, sources cited in the research pack):
  used imports from China to Russia Jan–Apr 2026: Corolla ~2,136 units (#1),
  Tayron ~1,988 (#2); May 2026 single month: Tayron 842 units (#1) vs Corolla
  592. Sources: Autonews; AUTOSTAT (May 2026: China ≈ 26.5% of Russian used
  imports). **Before any article cites these numbers, re-verify them against
  the cited sources.**
- After 3 vehicles, re-evaluate by Case Density: which vehicle has the most
  real CN repair cases, the biggest RU info gap, and the highest parts
  commercial value → that brand gets expanded.
