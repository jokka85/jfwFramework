<!-- BREAD CRUMB -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item <{CRUMB_HOME_ACTIVE}>" <{CRUMB_CURRENT_HOME}>>
            <a href="<{CRUMB_HOME_LINK}>">Home</a>
        </li>
        <{CRUMB_VARS}>
        <li style="visibility: <{CRUMB_PAGE_VISIBLE}>" class="breadcrumb-item <{CRUMB_PAGE_ACTIVE}>" <{CRUMB_PAGE_CURRENT}>>
            <a href="<{CRUMB_PAGE_LINK}>"><{CRUMB_PAGE_NAME}></a>
        </li>
        <{/CRUMB_VARS}>
    </ol>
</nav>