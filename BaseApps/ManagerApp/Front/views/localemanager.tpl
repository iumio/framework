{extends 'template.tpl'}
{block name="principal"}
    <div class="wrapper">
        {include file='partials/sidebar.tpl'}
        <div class="main-panel">
            <nav class="navbar navbar-default navbar-fixed">
                <div class="container-fluid">
                    <div class="navbar-header">
                        {include file='partials/toogle.tpl'}
                        <a class="navbar-brand" href="#">Locale Manager</a>
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
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title">Locale informations</h4>
                                    <p class="category">Informations about locale feature</p>
                                </div>
                                <div class="content ">
                                    <ul>
                                        <li>Locale status  : <span class="elemnt">{if true === $enabledlocale} Enabled {else} Disabled {/if} </span> </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title">Options</h4>
                                    <p class="category">Click on one of the buttons to perform an action.</p>
                                </div>
                                <div class="content">
                                    <div class="row center-block text-center manager-options">
                                        <div class="col-md-12">
                                            {if true === $enabledlocale}
                                            <h5 class="category">Disabled the locale on the framework instance to disabled it on each app</h5>
                                                <br>
                                            <a class="btn-default btn btn-danger changestatuslocaleevent" attr-href="{nocache}{route name='iumio_manager_locale_manager_framework_change_status' params=["status" => "disabled"] }{/nocache}" attr-mode="disable">Disabled locale</a>
                                            {else}
                                                <h5 class="category">Enabled the locale on the framework instance to enable it on each app</h5>
                                                <br>
                                                <a class="btn-default btn btn-success changestatuslocaleevent" attr-event="changestatuslocale" attr-href="{nocache}{route name='iumio_manager_locale_manager_framework_change_status' params=["status" => "enabled"] }{/nocache}" attr-mode="enable">Enabled locale</a>

                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {if true === $enabledlocale}
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title">List of locale configurations</h4>
                                    <p class="category">This is the locale for each app list. You have the main information on them such as the name etc.</p>
                                </div>
                                <div class="content table-responsive table-full-width">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                        <th>Locale registered</th>
                                        <th>Status</th>
                                        <th>App targeted</th>
                                        <th>Default locale</th>
                                        <th>Edit locale</th>
                                        <th>Disable locale</th>
                                        </thead>
                                        <tbody class="localeconfig" attr-href="{nocache}{route name='iumio_manager_locale_manager_get_all'}{/nocache}">

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>

            {include file='partials/footer.tpl'}

        </div>
    </div>
{/block}