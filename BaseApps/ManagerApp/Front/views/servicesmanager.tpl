{extends 'template.tpl'}
{block name="principal"}
    <div class="wrapper">
        {include file='partials/sidebar.tpl'}
    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    {include file='partials/toogle.tpl'}
                    <a class="navbar-brand" href="#">Service Manager</a>
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
                                <h4 class="title">Services statistics</h4>
                                <p class="category">The current statistics of your services</p>
                            </div>
                            <div class="content dashboardStats" attr-href="{nocache}{route name='iumio_manager_dashboard_get_statistics'}{/nocache}">
                                <ul>
                                    <li class="iumiohidden">Apps  : <span class="dashb-app">0</span> </li>
                                    <li class="iumiohidden">Apps enabled : <span class="dashb-appena">0</span></li>
                                    <li class="iumiohidden">Apps prefixed  : <span class="dashb-apppre">0</span></li>
                                    <li class="iumiohidden">Routes  : <span class="dashb-route">0</span></li>
                                    <li class="iumiohidden">Routes disabled : <span class="dashb-routedisa">0</span></li>
                                    <li class="iumiohidden">Routes with public visibility : <span class="dashb-routevisi">0</span></li>
                                    <li>Services  : <span class="dashb-services">0</span></li>
                                    <li>Services enabled : <span class="dashb-services-ena">0</span></li>
                                    <li class="iumiohidden">Routes with public visibility : <span class="dashb-routevisi">0</span></li>
                                    <li class="iumiohidden">Requests successful : <span class="dashb-reqsuc">0</span></li>
                                    <li class="iumiohidden">Errors : <span class="dashb-err">0</span></li>
                                    <li class="iumiohidden">Critical Errors (Error 500) : <span class="dashb-errcri">0</span></li>
                                    <li class="iumiohidden">Others Errors : <span class="dashb-erroth">0</span></li>
                                    <li class="iumiohidden">Databases connected : <span class="dashb-dbco">0</span></li>
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
                                        <a class="btn-default btn createservice"  attr-href="{nocache}{route name='iumio_manager_services_manager_create_service'}{/nocache}">Create a service</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">List of your services</h4>
                                <p class="category">This is the services declarations list. You have the main information on them such as status, namespace, etc. You can also perform actions on each service.<br> </p>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <th>Name</th>
                                    <th>Namespace</th>
                                    <th>Status</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                    </thead>
                                    <tbody class="serviceslist" attr-href="{nocache}{route name='iumio_manager_services_manager_get_all'}{/nocache}">
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