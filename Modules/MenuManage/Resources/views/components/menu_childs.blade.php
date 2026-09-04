@foreach ($items as $item)
    @if(sidebarPermission($item))
        <li class="dd-item" data-id="{{ $item->id }}">
            <div class="card accordion_card" id="accordion_{{ $item->id }}">
                <div class="card-header item_header" id="heading_{{ $item->id }}">
                    <div class="dd-handle">
                        <div class="float-left">
                            {{ $item->name }}
                        </div>
                    </div>
                    <div class="float-right btn_div">
                        <div class="edit_icon">
                            <i class="ti-close remove_menu"></i>
                        </div>
                    </div>
                </div>
            </div>
            @if($item->childs->count())
                <ol class="dd-list">
                    @include('menumanage::components.menu_childs', ['items' => $item->childs])
                </ol>
            @endif
        </li>
    @endif
@endforeach
