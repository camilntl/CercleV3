<?php

namespace App\Enum;

enum RoleUser: string
{
    case User = "ROLE_USER";
    case SuperAdmin = "ROLE_SUPER_ADMIN";
    case Admin = "ROLE_ADMIN";
    case COP = "ROLE_COP";
    case CAPA = "ROLE_CAPA";
    case BDE = "ROLE_BDE";
}