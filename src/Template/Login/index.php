    <script src="<{__JS_DIR__}>user.js"></script>

    <h1 class="mx-auto w-50 text-center"><{APP_NAME}> Login</h1>
    <div class="jumbotron p-5 m-5 w-75 mx-auto login-bg border">
        <form name="login" id="login" method="post" action="/<{__BASE_DIR__}>/login/?log_in=true">
            <input type="hidden" id="username_min" value="<{USER_MIN}>"/>
            <input type="hidden" id="username_max" value="<{USER_MAX}>"/>
            <input type="hidden" id="password_min" value="<{PASS_MIN}>" />
            <input type="hidden" id="password_max" value="<{PASS_MAX}>" />
            <div class="form-group">
                <label for="username">Username:</label>
                <input class="form-control" name="username" id="username" />
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input class="form-control" type="password" name="password" id="password" />
            </div>
            <div class="form-group">
                <button class="btn btn-outline-primary btn-lg btn-block" type="button" name="login-submit" id="login-submit">
                    Login
                </button>
        </form>
    </div>