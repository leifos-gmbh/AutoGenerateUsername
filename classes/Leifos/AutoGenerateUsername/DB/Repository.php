<?php

namespace Leifos\AutoGenerateUsername\DB;

use Leifos\AutoGenerateUsername\I\DB\Repository as lfAGURepositoryInterface;
use ilDBInterface;
use ilDBConstants;
use Leifos\AutoGenerateUsername\I\DB\User\Handler as lfAGUDBUserInterface;

class Repository implements lfAGURepositoryInterface
{
    protected ilDBInterface $db;

    public function __construct(ilDBInterface $db)
    {
        $this->db = $db;
    }

    public function generateLogin(lfAGUDBUserInterface $db_user): lfAGUDBUserInterface
    {
        $found = false;
        $postfix = 0;
        $c_login = $db_user->getName();
        while (!$found) {
            $r = $this->db->query("SELECT login FROM usr_data WHERE login = " .
                $this->db->quote($c_login) . ' ' .
                'AND usr_id != ' . $this->db->quote($db_user->getId(), ilDBConstants::T_INTEGER));
            if ($r->numRows() > 0) {
                $postfix++;
                $c_login = $db_user->getName() . $postfix;
            } else {
                $found = true;
            }
        }
        return $db_user->withLogin($c_login);
    }

    public function updateLogin(lfAGUDBUserInterface $db_user): void
    {
        $query = 'update usr_data set login = ' . $this->db->quote($db_user->getLogin(), 'text') . ' ' .
            'where usr_id = ' . $this->db->quote($db_user->getId(), 'integer');
        $this->db->manipulate($query);
    }
}