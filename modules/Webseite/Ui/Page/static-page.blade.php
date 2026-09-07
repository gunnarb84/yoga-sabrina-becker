<div class="yoga-section yoga-section--creme yoga-section--flush">
<div class="yoga-static-page">
    @if ($page === null)
        @php(abort(404))
    @endif

    @section('title', $page->titel . ' - Yoga mit Sabrina Becker')

    @if ($page->meta_beschreibung)
        @section('meta-description', $page->meta_beschreibung)
    @endif

    <h1>{{ $page->titel }}</h1>

    <div class="yoga-page-content">{!! $page->inhalt !!}</div>
</div>
</div>
