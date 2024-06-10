<?php

namespace Leifos\AutoGenerateUsername\I\Pattern\Node;

use Leifos\AutoGenerateUsername\I\Pattern\Node\Collection as lfAGUPatternNodeCollectionInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\EmailHandler as lfAGUPatternNodeEmailInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\FirstnameHandler as lfAGUPatternNodeFirstnameInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\HashHandler as lfAGUPatternNodeHashInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\LastnameHandler as lfAGUPatternNodeLastnameInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\LoginHandler as lfAGUPatternNodeLoginInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\MatriculationHandler as lfAGUPatternNodeMatriculationInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\NumberHandler as lfAGUPatternNodeNumberInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\TextHandler as lfAGUPatternNodeTextInterface;
use Leifos\AutoGenerateUsername\I\Pattern\Node\UDFHandler as lfAGUPatternNodeUDFInterface;

interface Factory
{
    public function collection(): lfAGUPatternNodeCollectionInterface;

    public function email(): lfAGUPatternNodeEmailInterface;

    public function firstname(): lfAGUPatternNodeFirstnameInterface;

    public function hash(): lfAGUPatternNodeHashInterface;

    public function lastname(): lfAGUPatternNodeLastnameInterface;

    public function login(): lfAGUPatternNodeLoginInterface;

    public function matriculation(): lfAGUPatternNodeMatriculationInterface;

    public function number(): lfAGUPatternNodeNumberInterface;

    public function text(): lfAGUPatternNodeTextInterface;

    public function udf(): lfAGUPatternNodeUDFInterface;
}