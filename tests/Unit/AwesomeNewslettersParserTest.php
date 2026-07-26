<?php

use App\Services\AwesomeNewslettersParser;

function parseReadme(): array {
    $markdown = <<<'MARKDOWN'
    A curated list of newsletters.

    - [Frontend](#frontend)
      - [JavaScript](#javascript)

    ## Frontend

    ### General Web Development

    - [Labnotes](https://labnotes.org/). A weekly email about software development.
    - [Front-end Front](https://frontendfront.us9.list-manage.com/subscribe?u=b033). [Frontendfront](https://frontendfront.com/)

    ### JavaScript

    - [JavaScript Weekly](https://javascriptweekly.com/). A once-weekly round-up of JavaScript news. [Archive](https://javascriptweekly.com/issues).
    - [Labnotes](https://labnotes.org/). Listed twice on purpose.

    ## License

    To the extent possible under law, [Dmitry Zudochkin](https://github.com/zudochkin) has waived all copyright.
    MARKDOWN;

    $parser = new AwesomeNewslettersParser;

    return $parser->parse($markdown);
}

test('it reads the name, the url and the category of a newsletter', function () {
    expect(parseReadme()[0])->toMatchArray(array(
        'name' => 'Labnotes',
        'url' => 'https://labnotes.org/',
        'description' => 'A weekly email about software development.',
        'author' => null,
        'category' => 'General Web Development',
    ));
});

test('it skips the table of contents', function () {
    $urls = array_column(parseReadme(), 'url');

    expect($urls)->not->toContain('#frontend')
        ->and($urls)->not->toContain('#javascript');
});

test('it reads the author when the entry ends with a lonely link', function () {
    expect(parseReadme()[1])->toMatchArray(array(
        'name' => 'Front-end Front',
        'author' => 'Frontendfront',
        'author_url' => 'https://frontendfront.com/',
    ));
});

test('it keeps the text of the inline links of a description', function () {
    expect(parseReadme()[2])->toMatchArray(array(
        'name' => 'JavaScript Weekly',
        'description' => 'A once-weekly round-up of JavaScript news. Archive.',
        'category' => 'JavaScript',
    ));
});

test('it lists a newsletter once, whatever the number of categories it belongs to', function () {
    $urls = array_column(parseReadme(), 'url');

    expect(count($urls))->toBe(count(array_unique($urls)));
});
