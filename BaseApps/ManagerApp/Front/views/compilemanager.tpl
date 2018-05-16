{extends 'template.tpl'}
{block name="principal"}
    <div class="wrapper">
        {include file='partials/sidebar.tpl'}
        <div class="main-panel">
            <nav class="navbar navbar-default navbar-fixed">
                <div class="container-fluid">
                    <div class="navbar-header">
                        {include file='partials/toogle.tpl'}
                        <a class="navbar-brand" href="#">Compiled Manager</a>
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
                                    <h4 class="title">Options</h4>
                                    <p class="category">Click on one of the buttons to perform an action.</p>
                                </div>
                                <div class="content">
                                    <div class="row center-block text-center manager-options">
                                        <div class="col-md-12">
                                            <a class="btn-default btn clearcompile"  attr-href="{nocache}{route name='iumio_manager_compile_manager_remove_all'}{/nocache}">Clear all</a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title">Compiled environments list</h4>
                                    <p class="category">This is the compiled environments list. You have the main information on them such as the size of the directory, permissions, etc. You can also perform actions on each compiled environment.</p>
                                </div>
                                <div class="content table-responsive table-full-width">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                        <th>Directory name</th>
                                        <th>Path</th>
                                        <th>Size</th>
                                        <th>Permissions</th>
                                        <th>Status</th>
                                        <th>Clear</th>
                                        </thead>
                                        <tbody class="getAllEnvCompile" attr-href="{nocache}{route name='iumio_manager_compile_manager_get_all'}{/nocache}">
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