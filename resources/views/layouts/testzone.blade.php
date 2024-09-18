        <div class="prev_container" id="prev_container">
            <div id="cursor_preview" class="cursor_preview">                              
            </div>
            <div class="container_preview">
                <div class="container">
                    <div class="row">
                        <div class="col-6">
                            <div class="c_preview" id="c_preview"></div>
                        </div>
                        <div class="col-6">
                            <div class="p_preview" id="p_preview"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col_100p">
                            <div class="test_area_preview">
                                <div class="banner_in_prev" id="banner_in_prev" style="cursor: none!important;">
                                    <div class="banner__prev_cur" id="banner__prev_cur"><img id="banner__prev_cur_img" class="banner__prev_cur_img" src=""></div>
                                    <div class="banner__title_2">@lang('collections.test_zone')</div>                             
                                    <a href="#"><div class="banner__description p-top_57" id="should_install" style="cursor:none !important;">@lang('collections.install_to_preview')</div></a>
                                    <div id="banner_cur_size" style="display: none"></div>
                                    <div class="arrow_down" id="arrow_for_install"><img class="arrow_down" src="/images/arrow_down.svg"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col_100p" id="mbtis">
                            <a id="banner_install_btn" style="cursor:pointer;" class="hvr-shutter-out-horizontal-g btn_add_prev" onclick="window.open(ext_link, '_blank')">@lang('collections.install')</a>
                        </div>
                    </div>                    
                </div>                 
                <!-- Next and previous buttons -->
                <a class="prev" onclick="plusSlides( - 1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>                
            </div>
        </div>