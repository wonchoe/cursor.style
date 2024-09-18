@if ($paginator->hasPages())

<div class="pagination-wrapper">
  <div class="pagination">    
    
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="prev page-numbers" aria-hidden="true">prev</span>
            @else
            <a class="prev page-numbers" href="{{ $paginator->previousPageUrl() }}">@lang('pagination.previous')</a>               
            @endif
            
 
            
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                   <span class="page-numbers">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="page-numbers current">{{ $page }}</span>
                        @else
                            <a class="page-numbers" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach            
            
    
    
            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a class="next page-numbers" href="{{ $paginator->nextPageUrl() }}">@lang('pagination.next')</a>
            @else
                <span class="prev page-numbers" aria-hidden="true">@lang('pagination.next')</span>
            @endif    
    
    
    
  </div>
</div>

@endif