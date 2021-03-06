import os


def from_env(name, fallback):
    value = os.environ.get(name)
    return value if value is not None else fallback


config = {
    "admin": from_env("MOSMETRO_ADMIN", "false"),
    "admin_username": from_env("MOSMETRO_ADMIN_USERNAME", "admin"),
    "admin_password": from_env("MOSMETRO_ADMIN_PASSWORD", "admin"),

    "redis": from_env("MOSMETRO_REDIS", "localhost"),
    "sql": from_env("MOSMETRO_SQL", "sqlite:///:memory:"),

    "metrics_port": from_env("MOSMETRO_METRICS_PORT", "9100"),

    "jenkins": {
        "url": from_env("MOSMETRO_JENKINS_URL", "https://jenkins.thedrhax.pw"),
        "project": from_env("MOSMETRO_JENKINS_PROJECT",
                            "mosmetro-android-pipeline"),

        # Ignore branches starting with '_'
        "private_branches": from_env("MOSMETRO_JENKINS_PRIVATE_BRANCHES",
                                     "true").lower() == "true"
    },

    "github": {
        "user": from_env("MOSMETRO_GITHUB_USER", "mosmetro-android"),
        "repo": from_env("MOSMETRO_GITHUB_REPO", "mosmetro-android"),
        "beta": from_env("MOSMETRO_GITHUB_BETA", "false").lower() == "true"
    },

    "stable_branches": from_env("MOSMETRO_BRANCHES_STABLE",
                                "play beta master").split(" ")
}
