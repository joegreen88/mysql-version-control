INSERT INTO `sections` (`id`, `slug`, `title`, `created_at`, `updated_at`)
VALUES
(1, 'news', 'News', now(), now()),
(2, 'tasks', 'Tasks', now(), now()),
(3, 'questions', 'Questions', now(), now());
    
INSERT INTO `articles` (`id`, `slug`, `title`, `body`, `section_id`, `created_at`, `updated_at`)
VALUES
(1, 'fizzbuzz-announces-new-website', 'FizzBuzz announces new website', '<p>FizzBuzz today announced plans to begin development of a new website.</p>\n<p>The new website will organize articles into sections.</p>\n<p>The development plan has been described by onlookers as ambitious and challenging.</p>', 1, now(), now());
