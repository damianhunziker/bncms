CHANGELOG
---------

Version 0.8

- Prohibit in displayTable renderings that elements receive an outline on focus
- DisplayTable Renderings now remember the state of search and sort criteria, paging and opened relations upon reloading of page or actialization of view. These things happen now through a new variable rootId not like before on displayTable instance level (ajaxExec), added it to the frontend to.
- Make displayTable to remember the actual state of openend instances of a page. Global variable rootId is created upon initial call of displayTable and is available for all subconainers during runtime. Using session variables in order to also remember after reload.
- Datetime Version Upgrade through Jan
- Add this Changelog file

Version 0.7

- Adding the web user functionality back to set rights for the frontendusers. Generic function generateUserDropdown() an in all places where user rights are set in the backend.
- Add escaping to variables in SQL-queries in many places.
- Webroot again returned to original State, where bncms Folder is included.
- Bugfix NtoM related data in display view the additional columns where shifted.
- Constraints for the conf-tables were added through Jan to the SQL install queries.
