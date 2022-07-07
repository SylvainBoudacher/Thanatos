<?php

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Repository\ModelRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;


class GetterService
{

    private CompanyRepository $companyRep;
    private UserRepository $userRep;
    private Security $security;

    public function __construct(CompanyRepository $companyRep, UserRepository $userRep, Security $security)
    {
        $this->companyRep = $companyRep;
        $this->userRep = $userRep;
        $this->security = $security;
    }

    public function getCompanyOfUser(): Company {
        return $this->companyRep->find($this->userRep->find($this->security->getUser())->getCompany());
    }

}