<?php

use App\Models\Setting;

test('setting can get and set values', function () {
    Setting::set('test_key', 'test_value');

    expect(Setting::get('test_key'))->toBe('test_value');
});

test('setting get returns default when key not found', function () {
    expect(Setting::get('nonexistent_key', 'default'))->toBe('default');
});

test('setting set updates existing key', function () {
    Setting::set('test_key', 'original');
    Setting::set('test_key', 'updated');

    expect(Setting::get('test_key'))->toBe('updated');
});

test('setting get group returns settings by group', function () {
    Setting::set('key_1', 'value_1', 'test_group');
    Setting::set('key_2', 'value_2', 'test_group');
    Setting::set('key_3', 'value_3', 'other_group');

    $group = Setting::getGroup('test_group');

    expect($group)->toHaveCount(2)
        ->and($group['key_1'])->toBe('value_1')
        ->and($group['key_2'])->toBe('value_2');
});

test('setting defaults returns all default values', function () {
    $defaults = Setting::defaults();

    expect($defaults)->toBeArray()
        ->and($defaults)->toHaveKey('app_name')
        ->and($defaults['app_name'])->toBe('Fluty Kos')
        ->and($defaults)->toHaveKey('app_tagline')
        ->and($defaults)->toHaveKey('late_fee_enabled');
});

test('setting getValue returns default when not in database', function () {
    expect(Setting::getValue('app_name'))->toBe('Fluty Kos');
});

test('setting getValue returns database value when set', function () {
    Setting::set('app_name', 'Custom Name');

    expect(Setting::getValue('app_name'))->toBe('Custom Name');
});

test('setting getValue returns null for unknown key', function () {
    expect(Setting::getValue('completely_unknown_key'))->toBeNull();
});
