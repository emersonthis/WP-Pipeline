Pipeline is a Wordpress plugin which allows you to create a dashboard for you gitHub project. The intended use case for this tool is to provide an intuitive interface for non-technical members of the team. It is not intended to be a SCRUM board, or a full-featured project management tool. The name Pipeline refers to the fact that we make it easy to "pipe in" information from gitHub. Which information, and how you present it is up to you. 

The goals was to be as versatile as possible while still being quick and easy to install and set up. The only requirements are:
* A gitHub repository
* A working Wordpress installation (docs [here](https://codex.wordpress.org/Installing_WordPress))
* PHP 5.5+

##Shortcodes

`[gh_issues labels="foo,bar" state="open|closed|all"]`

`[gh_milestones state="open|closed|all"]`

`[gh_searchform]`


## Requirements

## TODO:
* Make gitHub credentials dynamic
* Paginate results
* Update search to use new gitHub search endpoint
* Add assigned to search results?
* Remove 10 result limit on search
* View issue comments

## Backlog
* Make CSS inclusion optional
* Add admin option to hide gitHub credentials
* Add ability to
* Allow users to make comments
