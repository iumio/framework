{extends 'template.tpl'}
{block name="principal"}
    <div class="wrapper">
        {include file='partials/sidebar.tpl'}
        {nocache}
    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed {if $details['code'] == 500}navbar-ct-red{elseif $details['code'] == 200}navbar-ct-green{else}navbar-ct-orange{/if}">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#"> Generated at {$details['time']} - Event with uidie <strong>{$details['uidie']}</strong> </a>
                    <button type="button" class="navbar-toggle toggle-iumio-manager" data-toggle="collapse" data-target="#navigation-example-2" style="margin-top: 30px!important;margin-right: 20px!important;">
                        Menu
                    </button>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">
                    </ul>
                </div>

            </div>
        </nav>
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="header">
                                <h4 class="title text-center">Event characteristics</h4>
                                <p></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-config"></i> Uidie : <strong>{$details['uidie']}</strong></p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-target"></i> Event code : {$details['code']} {$details['code_title']}</p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-clock"></i> Date : {$details['time']|date_format:"%A, %B %e, %Y at %r"}</p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-magnet"></i> Method : {$details['method']}</p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-server"></i> Environment : {$env}</p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-link"></i> Path :  {$details['uri']} </p>
                                <hr>
                                <p class="category fs16"><i class="pe-7s-user"></i> Referer IP : { {$details['client_ip']} }</p>
                                <hr>
                                {if isset($details['type_error']) && $details['type_error'] != null} <p class="category fs16"><i class="pe-7s-close-circle"></i> Type : {$details['type_error']}</p><hr/>{/if}
                            </div>
                            <div class="content text-center">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title text-center">Event details</h4>
                            </div>
                            {if $details['code'] != 200}
                            <div class="content" style="padding-top: 0px">
                                <hr>
                                <p class="category fs16"><i class="pe-7s-info"></i> Message :  <span class="fw900 break-word">{$details['explain']}</span></h5></p>
                                <hr>
                                {if isset($details['file_error']) && $details['file_error'] != null}
                                <p class="category fs16 "><i class="pe-7s-paperclip"></i> File :  <span class="fs16 filelink ">{$details['file_error']}</span></p>

                                <hr>
                                {/if}

                                {if isset($details['line_error']) && $details['line_error']!= null}
                                <p class="category fs16 "><i class="pe-7s-target"></i> Line :  <span class="fw900">{$details['line_error']}</span></p>
                                <hr>
                                {/if}

                                <p class="category fs16 "><i class="pe-7s-magic-wand"></i> Solution :  <span class="">{$details['solution']}</span></p>
                                <hr>
                            </div>
                            {else}
                                <div class="content" style="padding-top: 0px">
                                    <hr>
                                    <p class="category fs16"><i class="pe-7s-info"></i> Message :  The request for URL  <strong>{$details['uri']}</strong> was a success</span></h5></p>
                                    <hr>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
            {include file='partials/footer.tpl'}

        </div>
        {/nocache}
    </div>
{/block}