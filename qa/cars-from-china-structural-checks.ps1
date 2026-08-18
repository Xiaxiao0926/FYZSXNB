$ErrorActionPreference = 'Stop'
$t = 'C:\Users\Administrator\Documents\Codex\2026-07-10\w\work\fyzsxnb-ui-v2\theme\fyzsxnb-neve-child'
$c = Get-Content "$t\inc\cars-from-china.php" -Raw

function Check($name, $cond) {
    if ($cond) { Write-Output "PASS  $name" } else { Write-Output "FAIL  $name" }
}

Check 'register fyz_vehicle' ([bool]($c -match "register_taxonomy\(\s*'fyz_vehicle'"))
Check 'register fyz_research_type' ([bool]($c -match "register_taxonomy\(\s*'fyz_research_type'"))
Check 'hierarchical true' ([bool]($c -match "'hierarchical'\s*=>\s*true"))
Check 'rewrite false (custom)' ([bool]($c -match "'rewrite'\s*=>\s*false"))
Check 'query_var fyz_vehicle' ([bool]($c -match "'query_var'\s*=>\s*'fyz_vehicle'"))
$rewriteCount = ([regex]::Matches($c, 'add_rewrite_rule\(')).Count
Check "5 rewrite rules (got $rewriteCount)" ($rewriteCount -eq 5)
Check 'RU hub template hook' ([bool]($c -match 'fyz_cfc_ru_hub'))
Check 'parent validation 404' ([bool]($c -match 'set_404'))
Check 'category 54 constant' ([bool]($c -match 'FYZSXNB_CFC_CATEGORY_RU'))
Check 'empty-section suppression' ([bool]($c -match "return ''; // Evidence-first"))
Check 'has_published (no dead links)' ([bool]($c -match 'fyzsxnb_cfc_has_published'))
Check 'initial matrix volkswagen' ([bool]($c -match "'volkswagen'"))
Check 'seed terms idempotent guard' ([bool]($c -match 'fyzsxnb_cfc_terms_seeded'))
Check 'research types map' ([bool]($c -match 'parts-compatibility'))
Check 'ru lang detection' ([bool]($c -match 'fyz_cfc_lang'))
Check 'no fabricated counts text' (-not [bool]($c -match 'known problems|17 known|常见故障'))
Check 'no commerce/Cart' (-not [bool]($c -match 'add to cart|SKU store|payment flow|checkout'))
$open = ([regex]::Matches($c, '\{')).Count
$close = ([regex]::Matches($c, '\}')).Count
Check "brace balance ($open/$close)" ($open -eq $close)

$f = Get-Content "$t\functions.php" -Raw
Check 'functions.php include cars-from-china' ([bool]($f -match 'inc/cars-from-china.php'))
Check 'functions.php enqueue cfc css' ([bool]($f -match 'fyzsxnb_cfc_enqueue_styles'))

foreach ($file in @('page-templates\cars-from-china-hub.php','taxonomy-fyz_vehicle.php','assets\css\cars-from-china.css')) {
    Check "file exists: $file" (Test-Path "$t\$file")
}
$hub = Get-Content "$t\page-templates\cars-from-china-hub.php" -Raw
Check 'hub template calls render_hub' ([bool]($hub -match 'fyzsxnb_cfc_render_hub'))
$tax = Get-Content "$t\taxonomy-fyz_vehicle.php" -Raw
Check 'taxonomy template brand/model dispatch' ([bool]($tax -match 'fyzsxnb_cfc_render_model') -and [bool]($tax -match 'fyzsxnb_cfc_render_brand'))
$css = Get-Content "$t\assets\css\cars-from-china.css" -Raw
Check 'css media queries (768/420)' ([bool]($css -match '768px') -and [bool]($css -match '420px'))
Check 'css overflow-safe (no fixed 4-digit px widths; media-query max-width excluded)' (-not [bool]($css -match '(?<!max-|min-)width:\s*\d{4}px'))
