<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use Leifos\AutoGenerateUsername\I\Pattern\Node\Collection as lfAGUPatternNodeCollectionInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\UDFHandler as lfAGUPatternNodeUDFInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\UDFHandler as lfAGUPatternNodeUDF;
use Leifos\AutoGenerateUsername\Pattern\Node\Collection as lfAGUPatternNodeCollection;
use Leifos\AutoGenerateUsername\I\Pattern\Node\EmailHandler as lfAGUPatternNodeEmailInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\EmailHandler as lfAGUPatternNodeEmail;
use Leifos\AutoGenerateUsername\I\Pattern\Node\Factory as lfAGUPatternNodeFactoryInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\FirstnameHandler as lfAGUPatternNodeFirstnameInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\FirstnameHandler as lfAGUPatternNodeFirstname;
use Leifos\AutoGenerateUsername\I\Pattern\Node\HashHandler as lfAGUPatternNodeHashInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\HashHandler as lfAGUPatternNodeHash;
use Leifos\AutoGenerateUsername\I\Pattern\Node\LastnameHandler as lfAGUPatternNodeLastnameInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\LastnameHandler as lfAGUPatternNodeLastname;
use Leifos\AutoGenerateUsername\I\Pattern\Node\LoginHandler as lfAGUPatternNodeLoginInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\LoginHandler as lfAGUPatternNodeLogin;
use Leifos\AutoGenerateUsername\I\Pattern\Node\MatriculationHandler as lfAGUPatternNodeMatriculationInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\MatriculationHandler as lfAGUPatternNodeMatriculation;
use Leifos\AutoGenerateUsername\I\Pattern\Node\NumberHandler as lfAGUPatternNodeNumberInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\NumberHandler as lfAGUPatternNodeNumber;
use Leifos\AutoGenerateUsername\I\Pattern\Node\TextHandler as lfAGUPatternNodeTextInterface;
use Leifos\AutoGenerateUsername\Pattern\Node\TextHandler as lfAGUPatternNodeText;

class Factory implements lfAGUPatternNodeFactoryInterface
{

    public function collection(): lfAGUPatternNodeCollectionInterface
    {
        return new lfAGUPatternNodeCollection();
    }

    public function email(): lfAGUPatternNodeEmailInterface
    {
        return new lfAGUPatternNodeEmail();
    }

    public function firstname(): lfAGUPatternNodeFirstnameInterface
    {
        return new lfAGUPatternNodeFirstname();
    }

    public function hash(): lfAGUPatternNodeHashInterface
    {
        return new lfAGUPatternNodeHash();
    }

    public function lastname(): lfAGUPatternNodeLastnameInterface
    {
        return new lfAGUPatternNodeLastname();
    }

    public function login(): lfAGUPatternNodeLoginInterface
    {
        return new lfAGUPatternNodeLogin();
    }

    public function matriculation(): lfAGUPatternNodeMatriculationInterface
    {
        return new lfAGUPatternNodeMatriculation();
    }

    public function number(): lfAGUPatternNodeNumberInterface
    {
        return new lfAGUPatternNodeNumber();
    }

    public function text(): lfAGUPatternNodeTextInterface
    {
        return new lfAGUPatternNodeText();
    }

    public function udf(): lfAGUPatternNodeUDFInterface
    {
        return new lfAGUPatternNodeUDF();
    }
}