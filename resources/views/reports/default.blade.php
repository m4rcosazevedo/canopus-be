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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        @if(!empty($queryDisplay))
            <div class="meta">
                @foreach($queryDisplay as $item)
                    <p><strong>{{ $item['name'] }}:</strong> {{ $item['value'] }}</p>
                @endforeach
            </div>
        @endif
    </div>

    @php
        $chunks = $fieldChunks ?? [$fields];
    @endphp

    @foreach($chunks as $index => $chunkFields)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <table>
            <thead>
                <tr>
                    @foreach($chunkFields as $field)
                        <th>{{ $field['title'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach($chunkFields as $field)
                            <td>{{ $row[$field['title']] ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>
