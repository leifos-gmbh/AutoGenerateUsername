<?php

namespace Leifos\AutoGenerateUsername\Pattern;

class Segments
{
    public const EMAIL = "\[email\]";
    public const FIRSTNAME = "\[firstname\]";
    public const HASH = "\[hash\]";
    public const LASTNAME = "\[lastname\]";
    public const LOGIN = "\[login\]";
    public const MATRICULATION = "\[matriculation\]";
    public const NUMBER = "\[number((:)([0-9]+)|(\+)([0-9]+))?\]";
    public const UDF = "\[udf_([0-9]+)\]";
}