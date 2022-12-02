# How to contribute ?
## 🚀 GIT Workflow
### • Issues
Create an issue for each feature or bug fix. One problem at a time. If you want to work on an issue, comment on the issue to let others know.

💡️**Note** : Most of the issues are fit to be worked alone (and to prevent merge conflicts). If you want to work on an issue with someone else, please mention it in the issue.


### • Branches
Each branch is named after the issue it is working on. For example, if you are working on issue #1 about a navbar bug, you would name your branch `001_debug_navbar`.

### • Commits
Commits names must be explicit and in english (no emojis). Each commit can be named this way : {action}{subject}. Example : Added `config.yml`

### • Pull Requests
When you are done working on your branch, you can create a pull request. The pull request must be linked to the issue it is working on. The pull request must be reviewed by at least one other person before being merged.

💡️**Note** : You must validate both SymfonyInsight and PHPUnit tests to validate the pull request and merge the branch.


## ✅ Code validation

### • SymfonyInsight
In order to prevent code quality issues, SymfonyInsight is used to check the code quality. Each time you push code to a branch (main or another), SymfonyInsight will run an analysis and perform a rating of the code quality. The rating is based on the number of issues found and the severity of these issues. The rating is displayed in the pull request and in the branch list.


❌ **If the rating is red**, you must fix the issues found before merging the pull request. **<ins>You CANNOT merge a pull request with a red rating.</in>**

️✅ **If the rating is green**, you can merge the pull request.

💡️**Note** : You must have a SymfonyInsight account and be connected to it to see the results of the analysis. Best wat to keep the code clean is to install the SymfonyInsight plugin in your IDE.


### • Tests
The command lines used to run tests in .bat script will be the same as implemented in GitHub (or GitLab) CI pipeline.

## 📄 Code
### • Indentation
The project code indentation used is **2 spaces**. You can change it in your IDE settings.

### • Naming convention
The project code naming convention used is **camelCase**. Variable names must be explicit and in english.

### • Comments
Comments must be explicit and in english. They must be used to explain the code and not to explain what the code does. For example, if you have a function that returns the number of users, you don't need to comment `// Returns the number of users`. You can comment `// Returns the number of users in the database`.

Your code can be explicit enough (and should) to not need comments. Most of the time, if you need to comment your code, it means that your code is not explicit enough.