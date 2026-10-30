
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Aperçu — {{ $page->title }}
    </title>

    @vite('resources/css/website-public.css')

    <style>
        .preview-toolbar {
            background: #172033;
            color: white;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .preview-toolbar a {
            display: inline-block;
            padding: 10px 16px;
            background: #2563eb;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 13px;
        }

        .preview-status {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .preview-content {
            max-width: 1000px;
            margin: 40px auto;
            padding: 35px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
        }

        .preview-content h1 {
            margin-bottom: 15px;
            font-size: clamp(25px, 4vw, 38px);
        }

        .preview-excerpt {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .preview-body {
            line-height: 1.9;
            overflow-wrap: anywhere;
        }

        @media(max-width: 600px) {
            .preview-toolbar {
                padding: 14px;
            }

            .preview-content {
                margin: 18px 12px;
                padding: 22px;
            }
        }
    </style>
</head>

<body>

    <div class="preview-toolbar">

        <div>
            <strong>WebStudio — Aperçu</strong>

            <span class="preview-status">
                {{ $page->status === 'published'
                    ? 'Page publiée'
                    : 'Brouillon' }}
            </span>
        </div>

        <a href="{{ route(
            'company.website.pages.edit',
            $page->id
        ) }}">
            ← Retour à la modification
        </a>

    </div>

    <main class="preview-content">

        <p>
            {{ $company->name }}
        </p>

        <h1>{{ $page->title }}</h1>

        @if($page->excerpt)
            <p class="preview-excerpt">
                {{ $page->excerpt }}
            </p>
        @endif


        <div class="preview-body">
            {!! \App\Support\WebsiteHtml::clean($page->content ?? '') !!}
        </div>

    </main>

</body>
</html>
