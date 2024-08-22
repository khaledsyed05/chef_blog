<?php

return [
    [
        'icon' => 'nav-icon fas fa-tachometer-alt',
        'route' => 'dashboard.dashboard',
        'title' => 'Dashboard',
        'active' => 'dashboard.dashboard',
    ],
    [
        'icon' => 'fas fa-tags nav-icon',
        'route' => 'dashboard.categories.index',
        'title' => 'Categories',
        'badge' => 'New',
        'active' => 'dashboard.categories.*',
        'ability' => 'categories.view',
    ],
    [
        'icon' => 'fas fa-box nav-icon',
        'route' => 'dashboard.recipes.index',
        'title' => 'recipes',
        'active' => 'dashboard.recipes.*',
        'ability' => 'recipes.view',
    ],
    [
        'icon' => 'fas fa-receipt nav-icon',
        'route' => 'dashboard.tags.index',
        'title' => 'tags',
        'active' => 'dashboard.tags.*',
        'ability' => 'tags.view',
    ],
    [
        'icon' => 'fas fa-receipt nav-icon',
        'route' => 'dashboard.articles.index',
        'title' => 'articles',
        'active' => 'dashboard.articles.*',
        'ability' => 'articles.view',
    ],
    [
        'icon' => 'fas fa-receipt nav-icon',
        'route' => 'dashboard.comments.index',
        'title' => 'comments',
        'active' => 'dashboard.comments.*',
        'ability' => 'comments.view',
    ],
];
