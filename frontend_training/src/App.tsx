import { useState } from "react";
import { v4 as uuidv4 } from "uuid";
import "./App.css";

function EditTodo({
  todo,
  saveEdit,
  cancelEdit
}: {
  todo: { id: string; name: string };
  saveEdit: (id: string, newName: string) => void;
  cancelEdit: (id: string) => void;
}) {
  const [editName, setEditName] = useState(todo.name);

  return (
    <div className="edit-container">
      {/* 横並びの要素 */}
      <input
        type="text"
        className="todo-input"
        value={editName}
        onChange={(e) => setEditName(e.target.value)}
      />
      <button className="update-button" onClick={() => saveEdit(todo.id, editName)}>
        更新
      </button>
      <button className="cancel-button" onClick={() => cancelEdit(todo.id)}>
        キャンセル
      </button>
    </div>
  );
}

function App() {
  const [name, setName] = useState<string>("");
  const [todos, setTodos] = useState<{ id: string; name: string; isComplete: boolean; isEdit: boolean }[]>([]);

  function toggleEdit(id: string) {
    setTodos(todos.map(todo =>
      ({ ...todo, isEdit: todo.id === id })
    ));
  }

  function saveEdit(id: string, newName: string) {
    setTodos(todos.map(todo =>
      todo.id === id ? { ...todo, name: newName, isEdit: false } : todo
    ));
  }

  function cancelEdit(id: string) {
    setTodos(todos.map(todo =>
      todo.id === id ? { ...todo, isEdit: false } : todo
    ));
  }

  return (
    <>
      <h1>TODOアプリ</h1>
      <div className="input">
        <input
          type="text"
          className="todo-input"
          value={name}
          onChange={(e) => setName(e.target.value)}
        />
        <button
          className="add-button"
          onClick={() => {
            if (name.trim() === "") return;
            setTodos([...todos, { id: uuidv4(), name, isComplete: false, isEdit: false }]);
            setName("");
          }}
        >
          追加
        </button>
      </div>

      <ul>
        {todos.map(todo => (
          <li
            key={todo.id}
            className={todo.isEdit ? "editing" : ""}>
            {todo.isEdit ? (
              <EditTodo
                todo={todo}
                saveEdit={saveEdit}
                cancelEdit={cancelEdit}
              />
            ) : (
              <>
                <span onClick={() => toggleEdit(todo.id)}>{todo.name}</span>
                <button
                  className="complete-button"
                  onClick={() => setTodos(todos.filter(t => t.id !== todo.id))}
                >
                  完了
                </button>
              </>
            )}
          </li>
        ))}
      </ul>
    </>
  );
}

export default App;
