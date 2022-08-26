<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFrontendediting\Listener;

use Alpdesk\AlpdeskFrontendediting\Utils\Utils;
use Contao\BackendUser;
use Contao\PageModel;

class BackendUserPermissions
{
    private ?BackendUser $backendUser = null;

    private bool $isAdmin = false;
    private bool $isAdminDisabled = false;

    /**
     * @return void
     * @throws \Exception
     */
    public function init(): void
    {
        try {

            $backendUser = BackendUser::getInstance();
            if (!$backendUser instanceof BackendUser) {
                throw new \Exception('invalid Usertype');
            }

            $this->backendUser = $backendUser;

            Utils::mergeUserGroupPersmissions($this->backendUser);

            $this->setIsAdmin($this->backendUser->isAdmin);

            if (
                $this->backendUser->alpdesk_fee_admin_disabled !== null &&
                (int)$this->backendUser->alpdesk_fee_admin_disabled === 1
            ) {
                $this->setIsAdminDisabled(true);
            }

        } catch (\Throwable $tr) {
            throw new \Exception($tr->getMessage());
        }

    }

    /**
     * @param string|null $field
     * @param string|null $method
     * @return bool
     */
    public function hasAccess(?string $field, ?string $method): bool
    {
        if ($field === null || $method === null) {
            return false;
        }

        if ($this->backendUser === null) {
            return false;
        }

        return $this->backendUser->hasAccess($field, $method);
    }

    public function hasPageMountAccess(PageModel $objPage): bool
    {
        if ($this->backendUser === null) {
            return false;
        }

        return Utils::hasPageMountAccess($objPage, $this->backendUser);
    }

    public function isAllowed(int $int, mixed $row): bool
    {
        if ($this->backendUser === null) {
            return false;
        }

        return Utils::isAllowed($int, $row, $this->backendUser);
    }

    /**
     * @return BackendUser|null
     */
    public function getBackendUser(): ?BackendUser
    {
        return $this->backendUser;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return bool
     */
    public function isAdminDisabled(): bool
    {
        return $this->isAdminDisabled;
    }

    /**
     * @param bool $isAdminDisabled
     */
    public function setIsAdminDisabled(bool $isAdminDisabled): void
    {
        $this->isAdminDisabled = $isAdminDisabled;
    }

}