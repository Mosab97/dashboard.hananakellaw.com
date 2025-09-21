<?php

namespace App\Traits;

use App\Models\PriceOffer;
use Illuminate\Support\Facades\Auth;

trait HasActionButtons
{
    public function getActionButtonsAttribute()
    {
        if (! Auth::user()->can(self::ui['s_lcf'].'_edit')) {
            return '';
        }

        $buttons = $this->getActionButtons();
        $filteredButtons = array_filter($buttons);

        // Wrap buttons in a flex container
        return '<div class="d-flex align-items-center gap-2">'.
            implode('', $filteredButtons).
            '</div>';
    }

    /**
     * Override this method in your model to customize buttons
     */
    protected function getActionButtons(): array
    {
        return [
            $this->getEditButton(),
            $this->getRemoveButton(),
            $this->getMenuButton(),
        ];
    }

    /**
     * Add custom button for price offer
     */
    protected function getShowButton($route, $attributes = null)
    {
        $title = t('Show '.self::ui['s_ucf']);
        $class = '';
        $icon = getSvgIcon('view', $title);

        return generateButton($route, $title, $class, $icon, $attributes);
    }

    protected function getEditButton($route = null)
    {
        if (! isset($route)) {
            $route = route(self::ui['route'].'.edit', ['_model' => $this->id]);
        }
        $title = t('Edit '.self::ui['s_ucf']);
        $class = 'btn_update_'.self::ui['s_lcf'];
        $icon = getSvgIcon('edit', $title);

        return generateButton($route, $title, $class, $icon);
    }

    protected function getRemoveButton($route = null, $attributes = null)
    {
        // i do it becuase of the attachemnt (has file name not name directly )
        if (! isset($attributes)) {
            $attributes = 'data-'.self::ui['s_ucf'].'-name="'.$this->name.'"';
        }
        if (! isset($route)) {
            $route = route(self::ui['route'].'.delete', ['_model' => $this->id]);
        }
        $title = t('Remove '.self::ui['s_lcf']);
        $class = 'btn_delete_'.self::ui['s_lcf'];
        $icon = getSvgIcon('delete', $title);

        return generateButton($route, $title, $class, $icon, $attributes);
    }

    protected function getMenuButton()
    {
        if (! Auth::user()->canAny([self::ui['s_lcf'].'_edit'])) {
            return '';
        }

        $menuTrigger = generateButton(
            '#',
            '',
            '',
            getSvgIcon('menu'/* , $title */),
            'data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end"'
        );

        $menuItems = $this->getMenuItems();

        return $menuTrigger.wrapInMenuContainer($menuItems);
    }

    /**
     * Override this method in your model to customize menu items
     */
    protected function getMenuItems(): string
    {
        return '';
    }

    /**
     * Add custom button for Email
     */
    protected function getEmailButton()
    {
        $route = route(PriceOffer::ui['route'].'.send_email', ['_model' => $this->id]);
        $title = t('Send Price Offer by Email');
        $class = 'btnSendEmail'.self::ui['s_ucf'];
        $icon = getSvgIcon('email', $title);
        $attributes = 'data-'.self::ui['s_ucf'].'-name="'.optional($this)->name.'"';

        return generateButton($route, $title, $class, $icon, $attributes);
    }
}
