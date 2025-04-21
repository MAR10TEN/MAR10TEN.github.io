function evaluateString() {
    const alphabet = document.getElementById("alphabet").value.split(",");
    const initialState = document.getElementById("initialState").value.trim();
    const finalStates = document.getElementById("finalStates").value.split(",").map(s => s.trim());
    const transitionsInput = document.getElementById("transitions").value.trim().split("\n");
    const inputString = document.getElementById("inputString").value.trim();
    const resultDiv = document.getElementById("result");
  
    const transitionMap = {};
    const edges = [];
    const states = new Set();
  
    transitionsInput.forEach(line => {
      const [from, symbol, to] = line.split(",");
      states.add(from);
      states.add(to);
  
      if (!transitionMap[from]) transitionMap[from] = {};
      if (!transitionMap[from][symbol]) transitionMap[from][symbol] = [];
  
      transitionMap[from][symbol].push(to);
  
      edges.push({ from, to, label: symbol });
    });
  
    const isAccepted = evaluateAFN(initialState, finalStates, transitionMap, inputString, alphabet);
  
    if (isAccepted) {
      resultDiv.textContent = `✅ Cadena ACEPTADA por el AFN.`;
      resultDiv.style.color = "green";
    } else {
      resultDiv.textContent = `❌ Cadena RECHAZADA por el AFN.`;
      resultDiv.style.color = "red";
    }
  
    drawGraph(states, edges, initialState, finalStates);
  }
  
  function evaluateAFN(initialState, finalStates, transitionMap, inputString, alphabet) {
    function dfs(currentState, index) {
      if (index === inputString.length) {
        return finalStates.includes(currentState);
      }
  
      const symbol = inputString[index];
      if (!alphabet.includes(symbol)) return false;
  
      const nextStates = transitionMap[currentState]?.[symbol] || [];
      for (let nextState of nextStates) {
        if (dfs(nextState, index + 1)) return true;
      }
  
      return false;
    }
  
    return dfs(initialState, 0);
  }
  
  function drawGraph(states, edges, initialState, finalStates) {
    const container = document.getElementById("automaton");
  
    const nodes = Array.from(states).map(state => ({
      id: state,
      label: state,
      color: finalStates.includes(state)
        ? { background: '#c0f0c0', border: '#2b7ce9' }
        : (state === initialState ? { background: '#fdfd96', border: '#ff9800' } : undefined),
      shape: 'ellipse'
    }));
  
    const data = {
      nodes: new vis.DataSet(nodes),
      edges: new vis.DataSet(edges)
    };
  
    const options = {
      layout: {
        hierarchical: {
          enabled: false
        }
      },
      edges: {
        arrows: {
          to: { enabled: true }
        },
        font: {
          align: "top"
        }
      },
      physics: {
        enabled: true,
        stabilization: {
          iterations: 1000
        }
      }
    };
  
    new vis.Network(container, data, options);
  }