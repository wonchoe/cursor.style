<div class="how">

  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol>
      <li><a href="/">@lang('messages.menu_main')</a></li>
      <li class="active">@lang('messages.menu_how_to_use')</li>
    </ol>
  </nav>

  <div class="gads-wrapper infeed" style="width:100%">
    <div class="googleads" style="width:100%">
      <!-- google ads here -->
      @include('other.google.infeed')
      <!-- google ads here -->
    </div>
  </div>


  <div class="how__wrap">
    <div class="how__btns">
      <div class="how__btn active">@lang('messages.t_2')</div>
      <div class="how__btn">@lang('messages.t_3')</div>
      <div class="how__btn">@lang('messages.t_4')</div>
      <div class="how__btn" style="margin-bottom: 10px;">@lang('messages.t_6')</div>
    </div>

    @php
    $locale = app()->getLocale();
    @endphp

    <div class="how__tabs">
      @foreach ([1, 2, 3, 4] as $i)
      <div class="how__tab {{ $i === 1 ? 'active' : '' }}">
      @if(View::exists("other.lang.$locale.howto.tab_$i"))
      @include("other.lang.$locale.howto.tab_$i")
    @else
      @include("other.lang.en_gb.howto.tab_$i")
    @endif
      </div>
    @endforeach
    </div>

  </div>
</div>