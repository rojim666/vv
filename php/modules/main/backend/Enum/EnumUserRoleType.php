<?php

namespace Modules\Main\Enum;

enum EnumUserRoleType: int
{
    case COPYWRITER_SPECIALIST = 1;//文案营销专员
    case COMMUNICATOR = 2;//沟通员
    case MANAGER = 3;//管理者
    case COPYWRITER_SUPERVISOR = 4;//文案主管
}
