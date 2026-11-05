<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Catalog Management')</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; color: #172033; background: #f3f5f9; }
        body { margin: 0; }
        header { background: #172033; color: white; }
        nav, main { width: min(100% - 2rem, 960px); margin: 0 auto; }
        nav { display: flex; gap: 1.25rem; padding: 1rem 0; }
        nav a { color: white; text-decoration: none; }
        main { padding: 2rem 0; }
        h1 { margin-top: 0; }
        a { color: #175cd3; }
        .panel { background: white; border: 1px solid #dfe3eb; border-radius: .5rem; padding: 1.25rem; margin-bottom: 1rem; }
        .toolbar, .actions { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; }
        .toolbar { justify-content: space-between; }
        label { display: block; font-weight: 600; margin: 1rem 0 .4rem; }
        input, select { box-sizing: border-box; width: 100%; max-width: 34rem; padding: .65rem; border: 1px solid #aeb8c8; border-radius: .3rem; }
        button, .button { display: inline-block; border: 0; border-radius: .3rem; background: #175cd3; color: white; padding: .65rem .9rem; text-decoration: none; cursor: pointer; font: inherit; }
        .button.secondary { background: #475467; }
        .button.danger, button.danger { background: #b42318; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .75rem; text-align: left; border-bottom: 1px solid #e4e7ec; vertical-align: middle; }
        .notice { padding: .85rem 1rem; border-radius: .35rem; margin: 1rem 0; }
        .success { background: #dcfae6; color: #05603a; }
        .error { background: #fee4e2; color: #912018; }
        .field-error { color: #b42318; margin: .35rem 0; }
        .muted { color: #667085; }
        @media (max-width: 640px) { table { font-size: .9rem; } th, td { padding: .55rem .35rem; } }
    </style>
</head>
<body>
    <header>
        <nav aria-label="Main navigation">
            <a href="{{ route('categories.index') }}">Categories</a>
            <a href="{{ route('subcategories.index') }}">Subcategories</a>
            <a href="{{ route('products.index') }}">Products</a>
        </nav>
    </header>
    <main>
        @if (session('success'))
            <div class="notice success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="notice error" role="alert">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
