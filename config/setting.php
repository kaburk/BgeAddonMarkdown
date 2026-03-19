<?php
/**
 * BgeAddonMarkdown
 *
 * @copyright     Copyright (c) kaburk
 * @link          https://github.com/kaburk/BgeAddonMarkdown
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Utility\Hash;
use Cake\Core\Configure;

// BurgerEditor のブロックタイプとして 'markdown' を追加
$config['Bge.enableAddonPlugin'] = Configure::read('Bge.enableAddonPlugin') ?? [];
$config['Bge.enableAddonPlugin'] = Hash::merge($config['Bge.enableAddonPlugin'], [
    'BgeAddonMarkdown',
]);

return $config;
