node('docker && rancher') {
    String cred_git = 'GitHub'
    String cred_github = 'GitHub-Token'

    String github_account = 'mosmetro-android'
    String github_repo = 'backend'
    String branch = 'master'

    String docker_image = 'thedrhax/mosmetro-backend'
    String rancher_url = 'http://rancher.thedrhax.pw:8080'
    String rancher_stack = 'mosmetro'
    String rancher_options = '--pull --force-upgrade --confirm-upgrade'

    String repo_url = 'git@github.com:' + github_account + '/' + github_repo + '.git'

    stage('Pull') {
        git branch: branch, credentialsId: cred_git, url: repo_url
        sh 'docker pull ' + docker_image + ':latest || true'
    }

    stage('Build') {
        sh 'docker build -f Dockerfile -t ' + docker_image + ':latest .'
    }

    stage('Push') {
        sh 'docker image push ' + docker_image + ':latest'
    }

    stage('Deploy') {
        env.RANCHER_URL = rancher_url
        env.MOSMETRO_ADMIN = 'true'
        withCredentials([
            usernamePassword(
                credentialsId: 'mosmetro',
                usernameVariable: 'MOSMETRO_ADMIN_USERNAME',
                passwordVariable: 'MOSMETRO_ADMIN_PASSWORD'
            ),
            usernamePassword(
                credentialsId: 'rancher',
                usernameVariable: 'RANCHER_ACCESS_KEY',
                passwordVariable: 'RANCHER_SECRET_KEY'
            )
        ]) {
            sh 'cd rancher && rancher-compose --project-name ' + rancher_stack + ' up -d ' + rancher_options
        }
    }

    stage('Notify') {
        githubNotify(
                status: "SUCCESS",
                credentialsId: cred_github,
                account: github_account,
                repo: github_repo,
                sha: branch
        )
        build job: 'MosMetro-Android-Backend', wait: false
    }
}
