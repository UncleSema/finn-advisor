<?php

namespace FinnAdvisor\Model;

class MessageTypeRegex
{
    const LIST_CATEGORIES = "/^категории$/";
    const NEW_CATEGORY = "/^\+\s+(\S+)$/";
    const REMOVE_CATEGORY = "/^\-\s+(\S+)$/";

    const HELP = "/^помощь$/";

    const NEW_OPERATION = "NEW_OPERATION";
    const FULL_STATEMENT = "FULL_STATEMENT";
    const CATEGORY_STATEMENT = "CATEGORY_STATEMENT";
    const REMAINDER = "REMAINDER";
    const REMOVE_OPERATION = "REMOVE_OPERATION";
}
