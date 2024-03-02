import java.awt.GridBagConstraints;  
import java.awt.GridBagLayout;  
import java.awt.*;
import java.awt.event.*;
import javax.swing.*;
import javax.swing.Timer;

import java.util.*;

public class CityPanel extends JPanel
{  
    final static boolean shouldFill = true;  
    final static boolean shouldWeightX = true;  
    final static boolean RIGHT_TO_LEFT = false;  
    private JButton simulate;
    private JPanel gate, farm, comdis2, park, res2, res1, governdis, comdis1, res3, landmark, port, buttonPanel;
    private JTextField personloc, leg1, leg2, leg3;
    private Graph g;
      
    public CityPanel() 
    {  
        if (RIGHT_TO_LEFT) {  
            setComponentOrientation(ComponentOrientation.RIGHT_TO_LEFT);  
        }  
                
        setLayout(new GridBagLayout());  
        GridBagConstraints c = new GridBagConstraints();  
        if (shouldFill) {   
            c.fill = GridBagConstraints.HORIZONTAL;  
        }  
        comdis1 = new JPanel();  
        c.weightx = 0.5;  
        c.ipady = 40;
        c.gridwidth = 2;
        c.fill = GridBagConstraints.HORIZONTAL; 
        c.gridx = 0;  
        c.gridy = 0;  
        comdis1.setBackground(Color.white);
        comdis1.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        comdis1.add(new JLabel("Commercial District 1"));
        add(comdis1, c); 
        
        res3 = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.weightx = 0.5;  
        c.ipady = 30;
        c.gridwidth = 1;
        c.gridx = 3;  
        c.gridy = 0;  
        res3.setBackground(Color.white);
        res3.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        res3.add(new JLabel("Residential District 3"));
        add(res3, c);  
        
        port = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL; 
        c.ipady = 40; 
        c.weightx = 0.5;  
        c.gridx = 4;  
        c.gridy = 0; 
        port.setBackground(Color.white);
        port.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        port.add(new JLabel("Port District")); 
        add(port, c); 
        
        governdis = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 40;        
        c.weightx = 1;  
        c.gridwidth = 2;  
        c.gridx = 0;  
        c.gridy = 1;  
        governdis.setBackground(Color.white);
        governdis.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        governdis.add(new JLabel("Government District"));
        add(governdis, c);  
        
        landmark = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 40;       
        c.weightx = 0.5;  
        c.gridwidth = 2;
        c.gridx = 3;  
        c.gridy = 1;  
        landmark.setBackground(Color.white);
        landmark.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        landmark.add(new JLabel("Landmark"));
        add(landmark, c);  

        res1 = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 80;       
        c.weightx = 0.5;  
        c.gridwidth = 1;
        c.gridx = 0;  
        c.gridy = 2; 
        res1.setBackground(Color.white); 
        res1.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        res1.add(new JLabel("Resident Dis. 1"));
        add(res1, c);  
        
        res2 = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 60;       
        c.weightx = 0.5;  
        c.gridwidth = 1;
        c.gridx = 1;  
        c.gridy = 2;  
        res2.setBackground(Color.white);
        res2.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        res2.add(new JLabel("Resident Dis. 2"));
        add(res2, c);  
        
        park = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 80;       
        c.weightx = 0.0;  
        c.gridwidth = 1;  
        c.gridx = 3;  
        c.gridy = 2;  
        park.setBackground(Color.white);
        park.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        park.add(new JLabel("Park"));
        add(park, c);

        farm = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 80;       
        c.weighty = 1.0;   
        c.gridwidth = 2;   
        c.gridheight = 2;
        c.gridx = 0;          
        c.gridy = 3;        
        farm.setBackground(Color.white);
        farm.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        farm.add(new JLabel("Farm Lands"));
        add(farm, c); 

        comdis2 = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 40;       
        c.weightx = 0;  
        c.gridheight = 1;
        c.gridwidth = 1;  
        c.gridx = 3;  
        c.gridy = 3;  
        comdis2.setBackground(Color.white);
        comdis2.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        comdis2.add(new JLabel("Commercial District 2"));
        add(comdis2, c);

        gate = new JPanel();  
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 40; 
        c.weightx = 0;  
        c.gridheight = 1;
        c.gridwidth = 1;  
        c.gridx = 3;  
        c.gridy = 4;  
        gate.setBackground(Color.white);
        gate.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        gate.add(new JLabel("Gate & Start location"));
        add(gate, c);
        
        simulate = new JButton("Start simulation");
        simulate.addActionListener(new ButtonListener());
        buttonPanel = new JPanel();
		buttonPanel.add(simulate);
        add(buttonPanel);
        
        personloc = new JTextField ("Person Location: "); 
        c.fill = GridBagConstraints.HORIZONTAL;  
        c.ipady = 40;        
        c.weighty = 1.0;     
        c.anchor = GridBagConstraints.PAGE_END;  
        c.insets = new Insets(10,0,0,0);   
        c.gridwidth = 5;   
        c.gridx = 0;         
        c.gridy = 6;       
        personloc.setBorder(BorderFactory.createLineBorder(Color.black, 2));
        add(personloc, c); 

        c.ipady = 0;        
        c.weighty = 0;     
        c.anchor = GridBagConstraints.PAGE_END;  
        c.gridwidth = 5;   
        c.gridx = 0;         

        leg1 = new JTextField ("Green: checked & found");     
        leg1.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        leg1.setBackground(new Color(0, 255, 0));
        c.gridy = 7;
        add(leg1,c); 
        leg2 = new JTextField ("Red: checked & not found");     
        leg2.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        leg2.setBackground(new Color(255, 0, 0));
        c.gridy = 8;
        add(leg2,c); 
        leg3 = new JTextField ("White: not checked");     
        leg3.setBorder(BorderFactory.createLineBorder(Color.gray, 1));
        leg3.setBackground(new Color(255, 255, 255));
        c.gridy = 9;
        add(leg3,c); 
    }  
   
    private class ButtonListener implements ActionListener 
   	{
      	public void actionPerformed(ActionEvent event)
      	{
            Random generator = new Random();
            int personLocation, start;
            JPanel[] panels = {gate, farm, comdis2, park, res2, res1, governdis, comdis1, res3, landmark, port};

            g = new Graph(11);  
            g.addEdge(0, 1);   
            g.addEdge(0, 2);   
            g.addEdge(1, 2);               
            g.addEdge(2, 3);   
            g.addEdge(3, 4);
            g.addEdge(3, 9);   
            g.addEdge(4, 5);   
            g.addEdge(5, 6);   
            g.addEdge(6, 9);   
            g.addEdge(6, 7);   
            g.addEdge(7, 8); 
            g.addEdge(8, 9);  
            g.addEdge(8, 10); 
            g.addEdge(9, 10);
			if (event.getSource() == simulate)
			{
                int Vertices= g.GetVertice();
                start=0;
                for(int i=0; i<11; i++)
                {
                    panels[i].setBackground(new Color(255, 255, 255));
                }
                personLocation = generator.nextInt(11);
                switch(personLocation)
                {
                    case 0: 
                        personloc.setText("Person Location: Gate & Start location");
                        break;
                    case 1: 
                        personloc.setText("Person Location: Farm Lands");
                        break;
                    case 2: 
                        personloc.setText("Person Location: Commercial District 2");
                        break;
                    case 3: 
                        personloc.setText("Person Location: Park");
                        break;
                    case 4: 
                        personloc.setText("Person Location: Residential District 2");
                        break;
                    case 5: 
                        personloc.setText("Person Location: Residential District 1");
                        break;
                    case 6: 
                        personloc.setText("Person Location: Government District");
                        break;
                    case 7: 
                        personloc.setText("Person Location: Commercial District 1");
                        break;
                    case 8: 
                        personloc.setText("Person Location: Residential District 3");
                        break;
                    case 9: 
                        personloc.setText("Person Location: Landmark");
                        break;
                    case 10: 
                        personloc.setText("Person Location: Port District");
                        break;
                }
              
				boolean[] visited = new boolean[Vertices];
                Queue<Integer> queue = new LinkedList<>();
                visited[start] = true;
                queue.add(start);
                int delay = 500;
                Timer timer = new Timer( delay, e -> 
                {
                    if (queue.size()<1) 
                    {
                        ((Timer)e.getSource()).stop();
                    }
                    int current = queue.poll();
                    
                    for (int neighbor : g.Getadj(current)) 
                    {
                        if (!visited[neighbor]) {
                            visited[neighbor] = true;
                            queue.add(neighbor);
                            panels[current].setBackground(new Color(255, 0, 0));
                        }
                    } 
                    if (current == personLocation) 
                    {
                        panels[current].setBackground(new Color(0, 255, 0));
                        ((Timer)e.getSource()).stop();
                    } 
                });
                timer.start(); 
			}
      	}
   	}
}  
