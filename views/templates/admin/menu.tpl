{if isset($menuItems) && $menuItems}
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    {foreach from=$menuItems item=menuItem}
                        <li{if $menuItem.active} class="active"{/if}>
                            <a href="{$menuItem.link}">
                                <i class="{$menuItem.icon}"></i>
                                {$menuItem.title}
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </nav>
{/if}
