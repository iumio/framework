{extends 'template.tpl'}
{block name="principal"}
    <div class="wrapper">
        {include file='partials/sidebar.tpl'}
        <div class="main-panel">
            <nav class="navbar navbar-default navbar-fixed">
                <div class="container-fluid">
                    <div class="navbar-header">
                        {include file='partials/toogle.tpl'}
                        <a class="navbar-brand" href="#">Composer Manager</a>
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
                                    <h4 class="title">Composer informations</h4>
                                    <p class="category">Informations about compoiser</p>
                                </div>
                                <div class="content">
                                    <ul>
                                        <li class="minimum-stability">Minimun stability  : <span class="rs">0</span> </li>
                                        <li class="platform">Plateform  : <ul class="rs"></ul></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title">List of composer dependencies</h4>
                                    <p class="category">This is the composer dependencies list. You have the main information on them such as the name, versions etc.</p>
                                    <div class="search-block col-md-4" style="padding: 10px 0px 10px 0px"><input type="text" id="composer-seach" placeholder="Search by name" class="form-control" /> </div>
                                </div>
                                <div class="content table-responsive table-full-width">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                        <th>Name</th>
                                        <th>Version</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Package for (dev or prod)</th>
                                        </thead>
                                        <tbody class="composerlist" attr-href="{nocache}{route name='iumio_manager_composer_manager_get_all'}{/nocache}">

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {include file='partials/footer.tpl'}

        </div>
    </div>
{/block}