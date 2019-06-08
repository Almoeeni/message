<style>
.badge1 {
   position:relative;
}
.badge1[data-badge]:after {
   content:attr(data-badge);
   position:absolute;
   top: 12px;
   right: 125px;
   font-size:.7em;
   background:green;
   color:white;
   width:18px;height:18px;
   text-align:center;
   line-height:18px;
   border-radius:50%;
   box-shadow:0 0 1px #333;
}
</style>
 <div class="roles-menu mt-0  col-3">
        <ul id="nav-tabs-wrapper" class="nav nav-tabs">
            <li class="nav-item"><a class="{{ request()->is('modules/inbox/employee-inbox') ? 'active' : '' }} badge1" data-badge="{{get_mesage_count()['inbox']}}" href="/modules/inbox/employee-inbox" id="inbox"><i class="fa fa-inbox pr-3"></i>Inbox  </a> </li>
            <li class="nav-item"><a class="{{ request()->is('modules/inbox/archived-messages') ? 'active' : '' }} badge1" data-badge="{{get_mesage_count()['archived']}}" href="/modules/inbox/archived-messages" id="archived"><i class="fa fa-file-text-o  pr-3"></i>Archived</a></li>
            <li class="nav-item"><a class="{{ request()->is('modules/inbox/delete-messages') ? 'active' : '' }} badge1" data-badge="{{get_mesage_count()['delete']}}" href="/modules/inbox/delete-messages" id="deleted"><i class="fa fa-trash pr-3"></i>Deleted </a></li>
        </ul>
    </div>