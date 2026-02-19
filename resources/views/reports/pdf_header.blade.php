<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 20px; }
        .meta { margin-bottom: 10px; font-size: 11px; color: #555; }
        .page-break { page-break-before: always; }
        h2 { margin-bottom: 8px; font-size: 11px; }
        .content { background: #f2f2f2; padding: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        @if(!empty($queryDisplay))
            <div class="meta">
                <h2>Filtros: </h2>
                <div class="content">
                    @foreach($queryDisplay as $item)
                        <p><strong>{{ $item['name'] }}:</strong> {{ $item['value'] }}</p>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
