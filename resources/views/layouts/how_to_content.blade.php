<div class="how">

  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol>
      <li><a href="/">@lang('messages.menu_main')</a></li>
      <li class="active">@lang('messages.menu_how_to_use')</li>
    </ol>
  </nav>




  <div class="how__wrap">
    <div class="how__btns">
      <div class="how__btn active">@lang('messages.t_2')</div>
      <div class="how__btn">@lang('messages.t_3')</div>
      <div class="how__btn">@lang('messages.t_4')</div>
      <div class="how__btn" style="margin-bottom: 10px;">@lang('messages.t_6')</div>
    </div>

    <div class="how__tabs">
      <div class="how__tab active">
        @include('other.lang.' . app()->getLocale() . '.messages.tab_1')
      </div>
      <div class="how__tab">
        @include('other.lang.' . app()->getLocale() . '.messages.tab_2')
      </div>
      <div class="how__tab">
        @include('other.lang.' . app()->getLocale() . '.messages.tab_3')
      </div>
      <div class="how__tab">
        @include('other.lang.' . app()->getLocale() . '.messages.tab_4')
      </div>
    </div>
  </div>
</div>