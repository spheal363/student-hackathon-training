#!/bin/bash

BASE_URL="http://localhost/todos"

# Function to check if the response is valid JSON
is_json() {
    echo "$1" | jq empty > /dev/null 2>&1
}

# Function to check if the response contains expected structure
check_json_structure() {
    echo "$1" | jq -e '.status == "ok" and (.data | type == "array") and (.data | length > 0)' > /dev/null 2>&1
}

# Function to test GET /todos (Retrieve all Todos)
test_get_all_todos() {
    echo "Testing GET /todos (Retrieve all Todos)"
    response=$(curl -s -w "\n%{http_code}" $BASE_URL)
    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')  # This removes the last line (the status code)

    if [ "$status_code" -eq 200 ] && is_json "$body"; then
        echo "✅ Passed"
        echo "Response: $body"
    else
        echo "❌ Failed"
        echo "Response: $body"
    fi
    echo ""  # New line after test case
}

# Function to test GET /todos?id=1 (Retrieve Todo by ID)
test_get_todo_by_id() {
    echo "Testing GET /todos?id=1 (Retrieve Todo by ID)"
    response=$(curl -s -w "\n%{http_code}" "$BASE_URL?id=1")
    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')  # This removes the last line (the status code)

    if [ "$status_code" -eq 200 ] && is_json "$body" && check_json_structure "$body"; then
        echo "✅ Passed"
        echo "Response: $body"
    else
        echo "❌ Failed"
        echo "Response: $body"
    fi
    echo ""  # New line after test case
}

# Function to test GET /todos?id=9999 (Not Found)
test_get_todo_not_found() {
    echo "Testing GET /todos?id=9999 (Not Found)"
    response=$(curl -s -w "\n%{http_code}" "$BASE_URL?id=9999")
    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')  # This removes the last line (the status code)

    if [ "$status_code" -eq 404 ] && is_json "$body"; then
        echo "✅ Passed"
        echo "Response: $body"
    else
        echo "❌ Failed"
        echo "Response: $body"
    fi
    echo ""  # New line after test case
}

# Function to test POST /todos (Create new Todo)
test_post_todo() {
    echo "Testing POST /todos (Create new Todo)"
    response=$(curl -s -w "\n%{http_code}" -X POST -H "Content-Type: application/json" -d '{"title": "New Todo"}' $BASE_URL)
    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')  # This removes the last line (the status code)

    if [ "$status_code" -eq 201 ] && is_json "$body" && check_json_structure "$body"; then
        echo "✅ Passed"
        echo "Response: $body"
    else
        echo "❌ Failed"
        echo "Response: $body"
    fi
    echo ""  # New line after test case
}

# Function to test PUT /todos?id=1 (Update Todo)
test_put_todo() {
    echo "Testing PUT /todos?id=1 (Update Todo)"
    response=$(curl -s -w "\n%{http_code}" -X PUT -H "Content-Type: application/json" -d '{"title": "Updated Title", "status": "completed"}' "$BASE_URL?id=1")
    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')  # This removes the last line (the status code)

    if [ "$status_code" -eq 200 ] && is_json "$body" && check_json_structure "$body"; then
        echo "✅ Passed"
        echo "Response: $body"
    else
        echo "❌ Failed"
        echo "Response: $body"
    fi
    echo ""  # New line after test case
}

# Function to test DELETE /todos?id=1 (Delete Todo)
test_delete_todo() {
    echo "Testing DELETE /todos?id=1 (Delete Todo)"
    response=$(curl -s -w "\n%{http_code}" -X DELETE "$BASE_URL?id=1")
    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')  # This removes the last line (the status code)

    if [ "$status_code" -eq 200 ] && is_json "$body" && check_json_structure "$body"; then
        echo "✅ Passed"
        echo "Response: $body"
    else
        echo "❌ Failed"
        echo "Response: $body"
    fi
    echo ""  # New line after test case
}

# Function to test GET /todos/cause-error (Server Error)
test_get_server_error() {
    echo "Testing GET /todos/cause-error (Server Error)"
    response=$(curl -s -w "\n%{http_code}" "$BASE_URL/cause-error")
    status_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')  # This removes the last line (the status code)

    if [ "$status_code" -eq 500 ] && is_json "$body"; then
        echo "✅ Passed"
        echo "Response: $body"
    else
        echo "❌ Failed"
        echo "Response: $body"
    fi
    echo ""  # New line after test case
}

# Function to display menu and execute selected test case
menu() {
    PS3="Please select a test case to run: "
    options=("Test GET /todos" "Test GET /todos?id=1" "Test GET /todos?id=9999" "Test POST /todos" "Test PUT /todos?id=1" "Test DELETE /todos?id=1" "Test GET /todos/cause-error" "Run All Tests" "Exit")
    select opt in "${options[@]}"
    do
        case $opt in
            "Test GET /todos")
                test_get_all_todos
                ;;
            "Test GET /todos?id=1")
                test_get_todo_by_id
                ;;
            "Test GET /todos?id=9999")
                test_get_todo_not_found
                ;;
            "Test POST /todos")
                test_post_todo
                ;;
            "Test PUT /todos?id=1")
                test_put_todo
                ;;
            "Test DELETE /todos?id=1")
                test_delete_todo
                ;;
            "Test GET /todos/cause-error")
                test_get_server_error
                ;;
            "Run All Tests")
                test_get_all_todos
                test_get_todo_by_id
                test_get_todo_not_found
                test_post_todo
                test_put_todo
                test_delete_todo
                test_get_server_error
                ;;
            "Exit")
                break
                ;;
            *) echo "Invalid option $REPLY";;
        esac
        # Always display the menu again after running a test
        echo ""
        menu
        break  # Exit the select loop after the test case runs
    done
}

# Run all tests by default if no input is provided
menu
