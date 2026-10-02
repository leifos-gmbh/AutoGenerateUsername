<?php

namespace Leifos\AutoGenerateUsername\Pattern;

class Segments
{
    public const string EMAIL = "\[email\]";
    public const string FIRSTNAME = "\[firstname\]";
    public const string HASH = "\[hash\]";
    public const string LASTNAME = "\[lastname\]";
    public const string LOGIN = "\[login\]";
    public const string MATRICULATION = "\[matriculation\]";
    public const string NUMBER = "\[number((:)([0-9]+)|(\+)([0-9]+))?\]";
    public const string UDF = "\[udf_([0-9]+)\]";
}
