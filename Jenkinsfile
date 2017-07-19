#!groovy

def phpVersion = "5.6"
def mysqlVersion = "5.5"

def launchStaticAnalysis = "yes"
def launchIntegrationTests = "yes"

class Globals {
    static pimVersion = "1.7"
    static extensionBranch = "dev-master"
    static phpVersion = "5.6"
    static mysqlVersion = "5.5"
}

stage("Checkout") {
    milestone 1
    if (env.BRANCH_NAME =~ /^PR-/) {
        userInput = input(message: 'Launch tests?', parameters: [
            string(defaultValue: "${Globals.extensionBranch}", description: 'Extension branch to build', name: 'extensionBranch'),
            choice(choices: 'yes\nno', description: 'Run static analysis', name: 'launchStaticAnalysis'),
            choice(choices: 'yes\nno', description: 'Run integration tests', name: 'launchIntegrationTests'),
        ])

        Globals.extensionBranch = userInput['extensionBranch']
        launchStaticAnalysis = userInput['launchStaticAnalysis']
        launchIntegrationTests = userInput['launchIntegrationTests']
    }
    milestone 2

    node {
        deleteDir()
        checkout scm
        stash "piivo_connector"

        checkout([
            $class: 'GitSCM',
            branches: [[name: "${Globals.pimVersion}"]],
            userRemoteConfigs: [[credentialsId: 'github-credentials', url: 'https://github.com/akeneo/pim-community-standard.git']]
        ])
        stash "pim_community"
    }
}

if (launchStaticAnalysis.equals("yes")) {
    stage("Static analysis tests") {
        runPhpCsFixerTest("5.6")
    }
}

if (launchIntegrationTests.equals("yes")) {
    stage("Integration tests") {
        def tasks = [:]

        tasks["phpunit-5.6-orm"] = {runPhpUnitTest("5.6", "orm")}

        parallel tasks
    }
}

def runPhpCsFixerTest(phpVersion) {
    node('docker') {
        deleteDir()
        try {
            docker.image("carcel/php:${phpVersion}").inside("-v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                unstash "piivo_connector"

                sh "php -d memory_limit=4G /usr/local/bin/composer install --optimize-autoloader --no-interaction --no-progress --prefer-dist"

                sh "mkdir -p app/build/logs/"
                sh "./bin/php-cs-fixer fix --dry-run --diff --format=junit --config=.php_cs.php > app/build/logs/phpcs.xml"
            }
        } finally {
            sh "docker stop \$(docker ps -a -q) || true"
            sh "docker rm \$(docker ps -a -q) || true"
            sh "docker volume rm \$(docker volume ls -q) || true"

            sh "sed -i \"s/testcase name=\\\"/testcase name=\\\"[php-${phpVersion}] /\" app/build/logs/*.xml"
            junit "app/build/logs/*.xml"
            deleteDir()
        }
    }
}

def runPhpUnitTest(phpVersion, storage) {
    node('docker') {
        deleteDir()
        try {
            docker.image("mysql:5.5").withRun("--name mysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_USER=akeneo_pim -e MYSQL_PASSWORD=akeneo_pim -e MYSQL_DATABASE=akeneo_pim") {
                docker.image("carcel/php:${phpVersion}").inside("--link mysql:mysql -v /home/akeneo/.composer:/home/akeneo/.composer -e COMPOSER_HOME=/home/akeneo/.composer") {
                    unstash "pim_community"

                    sh "composer require --no-update phpunit/phpunit:5.4 akeneo/piivo-connector:${Globals.extensionBranch}"
                    sh "php -d memory_limit=4G /usr/local/bin/composer install --ignore-platform-reqs --optimize-autoloader --no-interaction --no-progress --prefer-dist"

                    def extensionPath = "vendor/akeneo/piivo-connector/Piivo/Bundle/ConnectorBundle"
                    dir(extensionPath) {
                        deleteDir()
                        unstash "piivo_connector"
                    }
                    sh "composer dump-autoload -o"

                    sh "cp ${extensionPath}/tests/resources/parameters_test.yml app/config/parameters_test.yml"
                    sh "cat ${extensionPath}/tests/resources/routing.yml >> app/config/routing.yml"

                    sh "cp -r vendor/akeneo/extended-attribute-type/doc/example/Pim src/"
                    sh "sed -i 's#// your app bundles should be registered here#\\0\\nnew Piivo\\\\Bundle\\\\ConnectorBundle\\\\PiivoConnectorBundle(),new Pim\\\\Bundle\\\\ExtendedAttributeTypeBundle\\\\PimExtendedAttributeTypeBundle(),new Pim\\\\Bundle\\\\ExtendedCeBundle\\\\ExtendedCeBundle(),#' app/AppKernel.php"

                    sh "cat ${extensionPath}/tests/resources/config_test.yml >> app/config/config.yml"
                    sh "cp ${extensionPath}/tests/resources/phpunit.xml app/phpunit.xml"

                    sh "cat app/AppKernel.php"
                    sh "cat app/config/parameters_test.yml"
                    sh "cat app/config/pim_parameters.yml"

                    sh "rm ./app/cache/* -rf"
                    sh "./app/console --env=test pim:install --force -vvv"
                    sh "mkdir -p app/build/logs/"
                    sh "./bin/phpunit -c app/phpunit.xml --log-junit app/build/logs/phpunit.xml"
                }
            }
        } finally {
            sh "docker stop \$(docker ps -a -q) || true"
            sh "docker rm \$(docker ps -a -q) || true"
            sh "docker volume rm \$(docker volume ls -q) || true"

            deleteDir()
        }
    }
}
