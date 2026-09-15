<div class="permissioneditor <?= $this->previewMode ? 'control-disabled' : '' ?>" <?= $field->getAttributes() ?>>
    <table>
        <?php
<<<<<<< HEAD
        $globalIndex = 0;
        $checkboxMode = !($this->mode === 'radio');
=======
            $firstTab = true;
            $globalIndex = 0;
            $checkboxMode = !($this->mode === 'radio');
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
        ?>
        <?php foreach ($permissions as $tab => $tabPermissions): ?>
            <tr class="section">
                <th class="tab"><?= e(trans($tab)) ?></th>

<<<<<<< HEAD
                <th class="permission-type" title="<?= e(trans('backend::lang.user.permissions_toggle_section_allow')) ?>"><?= e(trans('backend::lang.user.allow')) ?></th>

                <?php if ($this->mode === 'radio'): ?>
                    <th class="permission-type" title="<?= e(trans('backend::lang.user.permissions_toggle_section_inherit')) ?>"><?= e(trans('backend::lang.user.inherit')) ?></th>
                    <th class="permission-type" title="<?= e(trans('backend::lang.user.permissions_toggle_section_deny')) ?>"><?= e(trans('backend::lang.user.deny')) ?></th>
=======
                <th class="permission-type"><?= $firstTab ? e(trans('backend::lang.user.allow')) : '' ?></th>

                <?php if ($this->mode === 'radio'): ?>
                    <th class="permission-type"><?= $firstTab ? e(trans('backend::lang.user.inherit')) : '' ?></th>
                    <th class="permission-type"><?= $firstTab ? e(trans('backend::lang.user.deny')) : '' ?></th>
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
                <?php endif; ?>

                <th></th>
            </tr>

            <?php
<<<<<<< HEAD
            $lastIndex = count($tabPermissions) - 1;
=======
                $lastIndex = count($tabPermissions) - 1;
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
            ?>
            <?php foreach ($tabPermissions as $index => $permission): ?>

                <?php
                $globalIndex++;

                switch ($this->mode) {
                    case 'radio':
                        $permissionValue = array_key_exists($permission->code, $permissionsData)
                            ? $permissionsData[$permission->code]
                            : 0;
                        break;
                    case 'switch':
                        $isChecked = !((int) @$permissionsData[$permission->code] === -1);
                        break;
                    case 'checkbox':
                    default:
                        $isChecked = array_key_exists($permission->code, $permissionsData);
                        break;
                }

                $allowId = $this->getId('permission-' . $globalIndex . '-allow');
                $inheritId = $this->getId('permission-' . $globalIndex . '-inherit');
                $denyId = $this->getId('permission-' . $globalIndex . '-deny');
                ?>

                <tr class="<?= $lastIndex == $index ? 'last-section-row' : '' ?>
                        <?= $checkboxMode ? 'mode-checkbox' : 'mode-radio' ?>
                        <?= $checkboxMode && !$isChecked ? 'disabled' : '' ?>
                        <?= !$checkboxMode && $permissionValue == -1 ? 'disabled' : '' ?>
                    ">

                    <td class="permission-name">
                        <?= e(trans($permission->label)) ?>
<<<<<<< HEAD
                        <?php if ($permission->comment): ?>
                            <span
                                class="text-info wn-icon-circle-info"
                                data-toggle="tooltip"
                                title="<?= e(trans($permission->comment)) ?>"
                                tabindex="0"
                                role="img"
                                aria-label="<?= e(trans($permission->comment)) ?>"
                            ></span>
                        <?php endif; ?>
=======
                        <p class="comment"><?= e(trans($permission->comment)) ?></p>
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
                    </td>

                    <?php if ($this->mode === 'radio'): ?>
                        <td class="permission-value">
                            <div class="radio custom-radio">
<<<<<<< HEAD
                                <input
=======
                                 <input
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
                                    id="<?= $allowId ?>"
                                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                                    value="1"
                                    type="radio"
                                    <?= $permissionValue == 1 ? 'checked="checked"' : '' ?>
                                    data-radio-color="green"
                                >

                                <label for="<?= $allowId ?>"><span>Allow</span></label>
                            </div>
                        </td>
                        <td class="permission-value">
                            <div class="radio custom-radio">
<<<<<<< HEAD
                                <input
=======
                                 <input
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
                                    id="<?= $inheritId ?>"
                                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                                    value="0"
                                    <?= $permissionValue == 0 ? 'checked="checked"' : '' ?>
                                    type="radio"
                                >

                                <label for="<?= $inheritId ?>"><span>Inherit</span></label>
                            </div>
                        </td>
                        <td class="permission-value">
                            <div class="radio custom-radio">
<<<<<<< HEAD
                                <input
=======
                                 <input
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
                                    id="<?= $denyId ?>"
                                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                                    value="-1"
                                    <?= $permissionValue == -1 ? 'checked="checked"' : '' ?>
                                    type="radio"
                                    data-radio-color="red"
                                >

                                <label for="<?= $denyId ?>"><span>Deny</span></label>
                            </div>
                        </td>
                    <?php elseif ($this->mode === 'switch'): ?>
                        <td class="permission-value">
                            <input
                                type="hidden"
                                name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                                value="-1"
                            >

                            <label class="custom-switch">
                                <input
                                    id="<?= $allowId ?>"
                                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                                    value="1"
                                    type="checkbox"
                                    <?= $isChecked ? 'checked="checked"' : '' ?>
                                >
                                <span><span><?= e(trans('backend::lang.list.column_switch_true')) ?></span><span><?= e(trans('backend::lang.list.column_switch_false')) ?></span></span>
                                <a class="slide-button"></a>
                            </label>
                        </td>
                    <?php else: ?>
                        <td class="permission-value">
                            <div class="checkbox custom-checkbox">
<<<<<<< HEAD
                                <input
=======
                                 <input
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
                                    id="<?= $allowId ?>"
                                    name="<?= e($baseFieldName) ?>[<?= e($permission->code) ?>]"
                                    value="1"
                                    type="checkbox"
                                    <?= $isChecked ? 'checked="checked"' : '' ?>
                                >

                                <label for="<?= $allowId ?>"><span>Allow</span></label>
                            </div>
                        </td>
                    <?php endif; ?>

                    <td></td>
                </tr>
            <?php endforeach ?>
<<<<<<< HEAD
=======

            <?php $firstTab = false; ?>
>>>>>>> 190bfe4f015fba0e5e4bad42e53137f7de7ac2d8
        <?php endforeach ?>
    </table>
    <div class="permissions-overlay"></div>
</div>
