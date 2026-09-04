<div class="tmp-sectionimg">
  <figure>
    <img @if (!empty(pagesetting('banner_image'))) src="{{ pagesetting('banner_image')[0]['thumbnail'] }}"
      alt="{{ __('Image') }}" />
    @else
    <img src="{{ assetPath('demo/images/banner-placeholder.jpg') }}" alt="{{ __('Image') }}" />
    @endif
    <figcaption>{{ pagesetting('caption') }}</figcaption>
  </figure>
</div>